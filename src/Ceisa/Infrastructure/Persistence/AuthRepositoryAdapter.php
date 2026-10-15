<?php

declare(strict_types=1);

namespace Src\Ceisa\Infrastructure\Persistence;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Src\Ceisa\Domain\Exceptions\AuthCredentialsNotFoundException;
use Src\Ceisa\Domain\Repositories\AuthRepositoryInterface;
use Src\Ceisa\Domain\ValueObjects\AuthApiKey;
use Src\Ceisa\Domain\ValueObjects\AuthCredentials;
use Src\Ceisa\Domain\ValueObjects\AuthLoginResult;
use Src\Ceisa\Domain\ValueObjects\KodeGudang;
use Throwable;

final readonly class AuthRepositoryAdapter implements AuthRepositoryInterface
{
    public const string CACHE_KEY = 'ceisa40_auth_login_result';

    private ?CacheRepository $cache;

    private ?ConnectionInterface $connection;

    public function __construct(
        ?CacheRepository $cache = null,
        ?ConnectionInterface $connection = null,
    ) {
        $this->cache = $cache;
        $this->connection = $connection;
    }

    public function storeLoginResult(KodeGudang $kodeGudang, AuthLoginResult $result): void
    {
        $cache = $this->resolveCache();
        $cacheKey = self::CACHE_KEY . '_' . $kodeGudang->value();
        $cache->put($cacheKey, $result->toArray(), max(1, $result->expiresIn));
    }

    public function getLoginResult(KodeGudang $kodeGudang): ?AuthLoginResult
    {
        $cache = $this->resolveCache();
        $cacheKey = self::CACHE_KEY . '_' . $kodeGudang->value();
        $data = $cache->get($cacheKey);

        if (! is_array($data)) {
            return null;
        }

        return AuthLoginResult::fromResponse($data);
    }

    public function getCredentials(KodeGudang $kodeGudang): AuthCredentials
    {
        $connection = $this->resolveConnection();

        $company = $connection->table('companies')
            ->where('tps_warehouse_code', $kodeGudang->value())
            ->first([
                'ceisa40_username',
                'ceisa40_password',
            ]);

        if ($company === null) {
            $company = $connection->table('companies')
                ->where('tps_code', $kodeGudang->value())
                ->first();
        }

        if ($company === null) {
            throw AuthCredentialsNotFoundException::withKodeGudang($kodeGudang);
        }

        if (empty($company->ceisa40_username) || empty($company->ceisa40_password)) {
            throw AuthCredentialsNotFoundException::missingCredentials($kodeGudang);
        }

        $password = (string) $company->ceisa40_password;
        try {
            $password = (string) Crypt::decrypt($password);
        } catch (Throwable) {
            // Keep original string if not encrypted
        }

        return AuthCredentials::fromStrings(
            (string) $company->ceisa40_username,
            $password,
        );
    }

    public function getApiKey(KodeGudang $kodeGudang): AuthApiKey
    {
        $connection = $this->resolveConnection();

        $company = $connection->table('companies')
            ->where('tps_warehouse_code', $kodeGudang->value())
            ->first(['ceisa40_api_key']);

        if ($company === null) {
            $company = $connection->table('companies')
                ->where('tps_code', $kodeGudang->value())
                ->first(['ceisa40_api_key']);
        }

        if ($company === null) {
            throw AuthCredentialsNotFoundException::withKodeGudang($kodeGudang);
        }

        return AuthApiKey::fromString((string) $company->ceisa40_api_key);
    }

    private function resolveCache(): CacheRepository
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        try {
            return Cache::store();
        } catch (Throwable) {
            // Container not booted
        }

        return app('cache.store');
    }

    private function resolveConnection(): ConnectionInterface
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        try {
            return DB::connection();
        } catch (Throwable) {
            // Container not booted
        }

        return app('db.connection');
    }
}
