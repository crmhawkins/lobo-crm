<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use DateTimeImmutable;

class MapKitController extends Controller
{
    public function getJwt()
    {
        $teamId = "TA457Z393T";
        $keyId = "72V3RT84MB";
        $privateKey = file_get_contents(storage_path('app/AuthKey_72V3RT84MB.p8')); // Asume que guardas tu clave privada en el almacenamiento de Laravel
    
        $configuration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($privateKey)
        );
    
        $now = new DateTimeImmutable();
    
        $token = $configuration->builder()
            ->withHeader('kid', $keyId)
            ->identifiedBy('123456') // ID único del token
            ->issuedBy($teamId)      // Team ID
            ->issuedAt($now)         // Token creado en
            ->expiresAt($now->modify('+1 hour')) // El token expira en 1 hora
            ->getToken($configuration->signer(), $configuration->signingKey());
    
        return response()->json(['token' => $token->toString()]);
    }
}