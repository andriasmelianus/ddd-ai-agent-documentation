<?php

declare(strict_types=1);

namespace Src\Shared\Framework\Infrastructure\Bus\QueryBus;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use RuntimeException;
use Src\Shared\Framework\Helpers\CacheHelper;

use function get_class;
use function is_callable;

/**
 * Implementasi konkret {@see QueryBusInterface}.
 *
 * Kelas ini bertugas me-resolve QueryHandler untuk setiap Query yang di-dispatch,
 * menjalankan handler tersebut, dan menyediakan beberapa variasi eksekusi
 * (standar, indexed, cached, orFail).
 *
 * ### Strategi Routing
 * 1. **Routing eksplisit** via properti statik {@see QueryBus::$routes}
 *    (pemetaan `class-string<QueryInterface>` → `class-string|callable`).
 * 2. **Konvensi penamaan**: mengganti akhiran `Query` pada nama kelas menjadi `Handler`
 *    (mis. `GetUserByIdQuery` → `GetUserByIdHandler`).
 * 3. Jika hasil route berupa `callable` (Closure/fungsi), langsung dipanggil dengan `$query`.
 *    Jika berupa nama kelas, di-resolve melalui `App::make()` lalu dipanggil `__invoke($query)`.
 *
 * Kelas ini `final` — untuk perilaku berbeda, buat implementasi baru dari `QueryBusInterface`.
 */
final class QueryBus implements QueryBusInterface
{
    /**
     * Pemetaan routing eksplisit dari kelas Query ke handler.
     *
     * Nilai dapat berupa nama kelas handler (`class-string<QueryHandlerInterface>`)
     * maupun callable/Closure. Dievaluasi sebelum konvensi penamaan otomatis.
     *
     * @var array<string, string|callable>
     */
    public static array $routes = [];

    /**
     * Menjalankan Query dan mengembalikan hasil pemrosesan handler.
     *
     * Alur:
     *  1. Ambil nama kelas Query via `get_class()`.
     *  2. Cari handler dari `self::$routes[$key]`; fallback ke konvensi `XxxQuery` → `XxxHandler`.
     *  3. Jika hasil `null`, lempar {@see RuntimeException}.
     *  4. Jika `is_callable`, panggil sebagai callable dengan argumen `$query`.
     *  5. Selain itu, resolve instance handler via `App::make()` dan panggil `__invoke($query)`.
     *
     * @param  QueryInterface  $query  Objek Query yang akan diproses.
     * @return mixed Hasil eksekusi handler (tipe bergantung pada Query yang di-dispatch).
     *
     * @throws RuntimeException Bila tidak ada handler yang dapat di-resolve untuk kelas Query.
     */
    public function query(QueryInterface $query)
    {
        $key = get_class($query);
        $queryHandlerName = self::$routes[$key] ?? preg_replace('/Query$/', 'Handler', $key);
        if ($queryHandlerName === null) {
            throw new RuntimeException('Handler not found for query: '.$key);
        }
        if (is_callable($queryHandlerName)) {
            return $queryHandlerName($query);
        }
        /** @var QueryHandlerInterface $queryHandler */
        $queryHandler = App::make($queryHandlerName);

        return $queryHandler->__invoke($query);
    }

    /**
     * Menjalankan Query dan memastikan hasil tidak `null` atau array kosong.
     *
     * Bila hasil dianggap kosong (nilai `null`, atau `array` dengan `count() === 0`),
     * melempar {@see ModelNotFoundException} yang di-serialize dari representasi JSON `$query`
     * sehingga pemanggil dapat memetakannya ke HTTP 404.
     *
     * @param  QueryInterface  $query  Query yang akan dijalankan.
     * @return mixed Hasil query yang dijamin tidak kosong.
     *
     * @throws ModelNotFoundException Bila hasil query kosong.
     * @throws RuntimeException Bila handler tidak dapat di-resolve.
     */
    public function queryOrFail(QueryInterface $query)
    {
        $res = $this->query($query);
        if ($res === null || (is_array($res) && count($res) === 0)) {
            throw new ModelNotFoundException((string) json_encode($query, JSON_THROW_ON_ERROR));
        }

        return $res;
    }

    /**
     * Menjalankan Query dengan caching hasil.
     *
     * Kunci cache dihitung sebagai `md5(get_class($query) . serialize($query))`
     * sehingga query dengan parameter identik menghasilkan cache hit.
     * Eksekusi ulang query hanya terjadi ketika kunci tidak tersedia di cache.
     *
     * @param  QueryInterface  $query  Query yang akan dijalankan.
     * @param  int|null  $ttl  TTL cache dalam detik. `null` diperlakukan sebagai `0`
     *                         (tanpa kadaluarsa, sesuai perilaku {@see CacheHelper::onceByKey()}).
     * @return mixed Hasil query, baik dari cache maupun eksekusi baru.
     */
    public function queryCached(QueryInterface $query, ?int $ttl = null)
    {
        $class = get_class($query);
        $key = md5($class.serialize($query));

        return CacheHelper::onceByKey($key, function () use ($query) {
            return $this->query($query);
        }, $ttl ?? 0);
    }

    /**
     * Menjalankan Query dan meng-index hasil koleksinya berdasarkan kolom tertentu.
     *
     * Menggunakan helper Laravel `collect($items)->keyBy($index)->toArray()` untuk
     * merekonstruksi array asosiatif di mana kunci adalah nilai field `$index`
     * pada setiap elemen. Cocok untuk lookup map berbasis ID.
     *
     * @param  QueryInterface  $query  Query yang akan dijalankan.
     * @param  string  $index  Nama field/atribut yang akan menjadi kunci array hasil.
     * @return array<int, mixed> Array hasil query yang telah di-key oleh `$index`.
     *
     * @throws QueryBusException Bila handler melempar error domain terkait query.
     */
    public function queryIndexed(QueryInterface $query, string $index): array
    {
        /** @var array<mixed> $items */
        $items = $this->query($query);
        /** @var array<int,mixed> $res */
        $res = collect($items)->keyBy($index)->toArray();

        return $res;
    }
}
