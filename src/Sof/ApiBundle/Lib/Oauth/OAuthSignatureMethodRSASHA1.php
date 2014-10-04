<?php

namespace Sof\ApiBundle\Lib\OAuth;

/**
 * The RSA-SHA1 signature method uses the RSASSA-PKCS1-v1_5 signature algorithm as defined in
 * [RFC3447] section 8.2 (more simply known as PKCS#1), using SHA-1 as the hash function for
 * EMSA-PKCS1-v1_5. It is assumed that the Consumer has provided its RSA public key in a
 * verified way to the Service Provider, in a manner which is beyond the scope of this
 * specification.
 *   - Chapter 9.3 ("RSA-SHA1")
 */
abstract class OAuthSignatureMethodRSASHA1 extends OAuthSignatureMethod {
    public function get_name() {
        return "RSA-SHA1";
    }

    // Up to the SP to implement this lookup of keys. Possible ideas are:
    // (1) do a lookup in a table of trusted certs keyed off of consumer
    // (2) fetch via http using a url provided by the requester
    // (3) some sort of specific discovery code based on request
    //
    // Either way should return a string representation of the certificate
    protected abstract function fetch_public_cert(&$request);

    // Up to the SP to implement this lookup of keys. Possible ideas are:
    // (1) do a lookup in a table of trusted certs keyed off of consumer
    //
    // Either way should return a string representation of the certificate
    protected abstract function fetch_private_cert(&$request);

    public function build_signature($request, $consumer, $token) {
        $base_string = $request->get_signature_base_string();
        $request->base_string = $base_string;

        // Fetch the private key cert based on the request
        $cert = $this->fetch_private_cert($request);

        // Pull the private key ID from the certificate
        $privatekeyid = openssl_get_privatekey($cert);

        // Sign using the key
        $ok = openssl_sign($base_string, $signature, $privatekeyid);

        // Release the key resource
        openssl_free_key($privatekeyid);

        return base64_encode($signature);
    }

    public function check_signature($request, $consumer, $token, $signature) {
        $decoded_sig = base64_decode($signature);

        $base_string = $request->get_signature_base_string();

        // Fetch the public key cert based on the request
        $cert = $this->fetch_public_cert($request);

        // Pull the public key ID from the certificate
        $publickeyid = openssl_get_publickey($cert);

        // Check the computed signature against the one passed in the query
        $ok = openssl_verify($base_string, $decoded_sig, $publickeyid);

        // Release the key resource
        openssl_free_key($publickeyid);

        return $ok == 1;
    }
}

class customOAuthSignatureMethod_RSA_SHA1 extends OAuthSignatureMethodRSASHA1 {
    protected function fetch_public_cert(&$request) {
        return $this->oauth_public_key();
    }
    protected function fetch_private_cert(&$request) {
        return '';
    }

    function oauth_public_key() {
        return <<< KEY
-----BEGIN CERTIFICATE-----
MIICSDCCAbGgAwIBAgIJAKbZQG/IMxhoMA0GCSqGSIb3DQEBBQUAMBwxGjAYBgNV
BAMTEXNieC1vc2FwaS5kbW0uY29tMB4XDTEyMTEwNjA1NDE0MloXDTE0MDUwODA1
NDE0MlowHDEaMBgGA1UEAxMRc2J4LW9zYXBpLmRtbS5jb20wgZ8wDQYJKoZIhvcN
AQEBBQADgY0AMIGJAoGBAJ+BXpHIZ9dyUwrdLQzicoNdsEaMtmoWYEapOXItbK+E
vyX4Lm6INEiyyKPLbGpwQ1ellBhVhYeG2O4dkRjgAGwCkimfbF7smIOk1WOpe9yP
hiPk8dpLF7JRsLerf6ftkdr4BJaM2nMZFmHdeYV1homJq0fiVjq1C95+J3FI5T8b
AgMBAAGjgZEwgY4wHQYDVR0OBBYEFPFQaiB57/ocMbQhcMQDCZ4rL2KBMEwGA1Ud
IwRFMEOAFPFQaiB57/ocMbQhcMQDCZ4rL2KBoSCkHjAcMRowGAYDVQQDExFzYngt
b3NhcGkuZG1tLmNvbYIJAKbZQG/IMxhoMAwGA1UdEwQFMAMBAf8wEQYJYIZIAYb4
QgEBBAQDAgEGMA0GCSqGSIb3DQEBBQUAA4GBAHcyZvJsz18oBljF86Y9x9iBGIIU
4zrGUPcRa+hdFJkT1vyhUrrL5qGnTkLeT4wxJ+rp+ZYsOQwxqPg6ywt/kjbVDzLW
NGVAiW6yvlcxstNw5uBAciCj2sKgenkBmLGZWjii9FUr97fbwVsuBgva6Loi5y1r
vSjrOEWFMGNz+PPc
-----END CERTIFICATE-----
KEY;
    }
}