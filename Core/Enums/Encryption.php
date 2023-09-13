<?php
namespace Core\Enums;

enum Encryption {
    case Algorithm;
    case Hash;
    case EncryptionKey;
    case Secret;
    case IV;

    public function get() :string {
        
        $encAlg ="AES-256-CBC";
        $hash = 'sha256';
        $encKEy = "gIVcwCv9zZ2fR8pkKHcH";

        $iv = substr($encKEy, 0, 14);
        $secret = hash($hash, $encKEy);
        $init_vector = substr(hash($hash, $iv), 0, 16);

        return match($this){
            Encryption::Algorithm =>$encAlg,
            Encryption::Hash => $hash,
            Encryption::Secret => $secret,
            Encryption::IV => $init_vector
        };
    }
}