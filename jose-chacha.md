%%%

    title = "Chacha derived AEAD algorithms in JSON Object Signing and Encryption (JOSE)"
    abbr = "JoseChaCha"
    category = "info"
    docname = "draft-amringer-jose-chacha-01"
    workgroup = "(No Working Group)"
    keyword = ["encryption", "AEAD", "cryptography", "security", "authenticated encryption", "jose"]

    date = 2019-07-23T23:51:00Z

    [[author]]
    initials="G."
    surname="Amringer"
    fullname="Guillaume Amringer"
      [author.address]
      email = "g.amringer@gmail.com"
      [author.address.postal]
      country = "Canada"

%%%

.# Abstract

This document defines how to use the AEAD algorithms "AEAD_XCHACHA20_POLY1305"
and "AEAD_CHACHA20_POLY1305" from [@?RFC8439] and
[@?I-D.irtf-cfrg-xchacha] in JSON Object Signing and Encryption (JOSE).

{mainmatter}

# Introduction

The Internet Research Task Force (IRTF) Crypto Forum Research Group
(CFRG) defined the ChaCha20 and Poly1305 algorithms to be used in IETF protocols
both independantly and as an AEAD construction ([@!RFC8439]).  It has also been
presented with a definition of an eXtended-nonce variant
([@!I-D.irtf-cfrg-xchacha]) for use in stateless contexts.  This document
defines how to use those algorithms in JOSE in an interoperable manner.

This document defines the conventions to use in the context of
[@!RFC7516], and [@!RFC7517].

## Notation and Conventions

The key words "**MUST**", "**MUST NOT**", "**REQUIRED**", "**SHALL**",
"**SHALL NOT**", "**SHOULD**", "**SHOULD NOT**", "**RECOMMENDED**", "**MAY**",
and "**OPTIONAL**" in this document are to be interpreted as described in
[@!RFC2119].

The JOSE key format ("JSON Web Key (JWK)") is defined by [@!RFC7517] and
thumbprints for it ("JSON Web Key (JWK) Thumbprint") in [@!RFC7638].

# Key Encryption

## Algorithms

This section defines the specifics of encrypting a JWE Content Encryption Key
(CEK) with AEAD_CHACHA20_POLY1305 [@!RFC8439] and AEAD_XCHACHA20_POLY1305
[@!I-D.irtf-cfrg-xchacha].

Use of an Initialization Vector (IV) is REQUIRED with this algorithm.  The IV is
represented in base64url-encoded form as the "iv" (initialization vector) Header
Parameter value.

The Additional Authenticated Data value used is the empty octet string.

The JWE Encrypted Key value is the ciphertext output.

The Authentication Tag output is represented in base64url-encoded form as the
"tag" (authentication tag) Header Parameter value.

The following "alg" (algorithm) Header Parameter values are used to indicate
that the JWE Encrypted Key is the result of encrypting the CEK using the
corresponding algorithm and IV size:

| Algorithm | IV size | "alg" value |
| ----------------------- | -------- | ----------- |
| AEAD_CHACHA20_POLY1305 | 96 bits | C20PKW |
| AEAD_XCHACHA20_POLY1305 | 192 bits | XC20PKW |

## Header Parameters Used for Key Encryption

The following Header Parameters are used for both algorithms defined for key
encryption.

### "iv" (Initialization Vector) Header Parameter

The "iv" (initialization vector) Header Parameter value is the base64url-encoded
representation of the 96-bit or 192-bit IV value used for the key encryption
operation.  This Header Parameter MUST be present and MUST be understood and
processed by implementations when these algorithms are used.

### "tag" (Authentication Tag) Header Parameter

The "tag" (authentication tag) Header Parameter value is the base64url-encoded
representation of the 128-bit Authentication Tag value resulting from the key
encryption operation.  This Header Parameter MUST be present and MUST be
understood and processed by implementations when these algorithms are used.

# Key Agreement with Elliptic Curve Diffie-Hellman Ephemeral Static

This section defines the specifics of key agreement with Elliptic Curve
Diffie-Hellman Ephemeral Static [@!RFC6090], in combination with the Concat KDF,
as defined in
[Section 5.8.2.1 of NIST.800-56A](https://csrc.nist.gov/publications/detail/sp/800-56a/rev-3/final)
for use as a symmetric key to wrap the CEK with the "C20PKW", or "XC20PKW"
algorithms, in the Key Agreement with Key Wrapping mode.

This mode is used exactly as defined in
[Section 4.6 of RFC7518](https://tools.ietf.org/html/rfc7518#section-4.6),
except that the combined key wrapping algorithms are the ones indicated in this
document. All headers pertaining to both the ECDH-ES and key wrapping components
("iv",' "tag", "epk", "apu", "apv") have the same meaning and requirement as in
their original definitions.

The following "alg" (algorithm) Header Parameter values are used to indicate
that the JWE Encrypted Key is the result of encrypting the CEK using the
corresponding algorithm:

| "alg" value | Key Management Algorithm |
| ----------- | ------------------------ |
| ECDH-ES+C20PKW | ECDH-ES using Concat KDF and CEK wrapped with C20PKW |
| ECDH-ES+XC20PKW | ECDH-ES using Concat KDF and CEK wrapped with XC20PKW |

# Content Encryption

## Algorithms

This section defines the specifics of performing authenticated encryption with
ChaCha20-Poly1305.

The CEK is used as the encryption key.

Use of an IV is REQUIRED with this algorithm.

The following "enc" (encryption algorithm) Header Parameter values
are used to indicate that the JWE Ciphertext and JWE Authentication
Tag values have been computed using the corresponding algorithm and
IV size:

| Algorithm | IV size | "alg" value |
| ----------------------- | -------- | ----------- |
| AEAD_CHACHA20_POLY1305 | 96 bits | C20P |
| AEAD_XCHACHA20_POLY1305 | 192 bits | XC20P |


# IANA Considerations

The following is added to the "JSON Web Signature and Encryption Algorithms"
registry:

o Algorithm Name: "C20PKW"  
o Algorithm Description:  Key wrapping with ChaCha20-Poly1305  
o Algorithm Usage Location(s): "alg"  
o JOSE Implementation Requirements: Optional  
o Change Controller: IESG  
o Specification Document(s): Section 2 of [RFC-THIS]  
o Algorithm Analysis Documents(s): [@!RFC8439]  

o Algorithm Name: "XC20PKW"  
o Algorithm Description:  Key wrapping with XChaCha20-Poly1305  
o Algorithm Usage Location(s): "alg"  
o JOSE Implementation Requirements: Optional  
o Change Controller: IESG  
o Specification Document(s): Section 2 of [RFC-THIS]  
o Algorithm Analysis Documents(s): [@?I-D.irtf-cfrg-xchacha]  

o Algorithm Name: "ECDH-ES+C20PKW"  
o Algorithm Description: ECDH-ES using Concat KDF and "C20PKW" wrapping  
o Algorithm Usage Location(s): "alg"  
o JOSE Implementation Requirements: Optional  
o Change Controller: IESG  
o Specification Document(s): Section 3 of [RFC-THIS]  
o Algorithm Analysis Documents(s): n/a  

o Algorithm Name: "ECDH-ES+XC20PKW"  
o Algorithm Description: ECDH-ES using Concat KDF and "XC20PKW" wrapping  
o Algorithm Usage Location(s): "alg"  
o JOSE Implementation Requirements: Optional  
o Change Controller: IESG  
o Specification Document(s): Section 3 of [RFC-THIS]  
o Algorithm Analysis Documents(s): n/a  

o Algorithm Name: "C20P"  
o Algorithm Description:  ChaCha20-Poly1305  
o Algorithm Usage Location(s): "enc"  
o JOSE Implementation Requirements: Optional  
o Change Controller: IESG  
o Specification Document(s): Section 4 of [RFC-THIS]  
o Algorithm Analysis Documents(s): [@!RFC8439]  

o Algorithm Name: "XC20P"  
o Algorithm Description:  ChaCha20-Poly1305  
o Algorithm Usage Location(s): "enc"  
o JOSE Implementation Requirements: Optional  
o Change Controller: IESG  
o Specification Document(s): Section 4 of [RFC-THIS]  
o Algorithm Analysis Documents(s): [@?I-D.irtf-cfrg-xchacha]  

{backmatter}

# Test Case for "XC20PKW"

A sample envelop using key wrapping with XChaCha20-Poly1305 JWE format
 as per [@!RFC7517] is shown below.

In this example, a producer Alice is encrypting content to a consumer 
Bob. The producer (Alice) generates an ephemeral key for the key 
agreement computation. Alice's and Bob's ephemeral keys are displayed 
below to encrypt and decrypt the following payload:

| Sample payload to encrypt | base64Url encoded |
| ----------------------- | ----------------------- |
| Hello World! | SGVsbG8gV29ybGQh |

Using the following (Curve25519) ephemeral keys (all keys defined below 
are base64Url encoded):

| Key type | value |
| ----------------------- | -------- |
| Alice's SecretKey | dn5T9HDBi71zlJph7h9KnuS7pcN1lGVExazNha4NNoI |
| Alice's PublicKey | gmoy3KdDhRu-TyFE06cRZ58YA-wpqRueq3tiKyT8lyY |
| Bob's SecretKey | _O82mTRkDkvkjjW7B57vRi39zZ7fUoJKdfOwjMcdgbY |
| Bob's PublicKey | mm30B1X2pJ7o_4NXBuYLmkCFv4jNoRwelGJ6H3BwplQ |

With the following JWE HEADERS:

 ```
{
      "typ": "prs.hyperledger.aries-auth-message",
      "alg": "ECDH-SS+XC20PKW",
      "enc": "XC20P"
}
``` 

And the Raw AAD for Bob (using his base58 encoded public key as 
recipient key) is (base64Url encoded) :
`BlrAXgMxBU2dw0RCbOchXGKyHN2mTQcoU3EKvnpfGl8`

Note: If the message is addressed to more than one recipient, then the 
keys of all recipients are base58 encoded and string sorted. They are 
then appended with a `.` separator and base64Url encoded to form the 
raw AAD.

The format of a raw AAD would be 
`base64UrlEncode(sha256(implode(sort([base58Encode(recipient1PubKey),
    base58Encode(recipient2PubKey) ...]), '.')))`
    
The above pseudo code assembles the AAD as follows:
```
1. Sort a list of (recipients) base58 encoded public keys (string sort)
2. Concatenate the ordered keys with a dot '.' separator and return the 
result as a string (implode function)
3. Sha256 hash the string
4. Return the base64Url encoding of the above hashed string
```

The final AAD for the payload encryption will then be (JWE Headers 
encoded + '.' + above Raw AAD) :
`eyJ0eXAiOiJwcnMuaHlwZXJsZWRnZXIuYXJpZXMtYXV0aC1tZXNzYWdlIiwiYWxnIjoiRUN \
ESC1TUytYQzIwUEtXIiwiZW5jIjoiWEMyMFAifQ.BlrAXgMxBU2dw0RCbOchXGKyHN2mTQco \
U3EKvnpfGl8`

Using the following nonce (base64Url encoded):
`oULwGExZbXZ431Pj0u9LxzfWg0hDb0tv`

Finally, using a generated CEK to encrypt the payload with the above AAD, 
Alice keys and Bob's public key, we get the following Envelop (` \` 
separator added for conciseness):

```
{
        "protected": "eyJ0eXAiOiJwcnMuaHlwZXJsZWRnZXIuYXJpZXMtYXV0aC1tZX \
NzYWdlIiwiYWxnIjoiRUNESC1TUytYQzIwUEtXIiwiZW5jIjoiWEMyMFAifQ",
        "recipients": [
            {
                "encrypted_key": "S4y0MPorZUmiR6njWAawSbaMIarc4m8_gNENiOUZwVc",
                "header": {
                    "apu": "DPFu5x4qiJKVFICr0RG18BW1fIIoC-rJa_6qSqVoBJpY \
15-svjXtp-6v_kAB5TlCBZqR3Ua9h-Vto1iRryIiAQ",
                    "iv": "YkTx0vfSgfxS1lgNdyN1aDGhti0qCEFq",
                    "tag": "6a_hwoO4nyfx8vQt5MbguA",
                    "kid": "BPq2fQ1QjDgAiUHU5LhMcbc6zJPkBimDz2yKagVkdu9d",
                    "spk": "eyJ0eXAiOiJqb3NlIiwiY3R5IjoiandrK2pzb24iLCJh \
bGciOiJFQ0RILUVTK1hDMjBQS1ciLCJlbmMiOiJYQzIwUCIsImVwayI6eyJrdHkiOiJPS1Ai \
LCJjcnYiOiJYMjU1MTkiLCJ4IjoicHBfOGw5YjdtMmJzMkFDMFVwV3lMbzUycXR3YXF4Uzk5 \
Q0MzQU0tS0RUbyJ9LCJpdiI6ImtJU3J6ejY3Smg5TVg1NkZkVzBiZWhWTGFJcDJFM01ZIiwi \
dGFnIjoiaVp5X1R0bzBRU3VKZzJLUGtONm5NUSJ9.X1uK0GflqVAEm5Knd5FQ2t8w-6KZivu \
bHmvXbzodqA0.Mx2rCJiwXKkh4r59PK_SNwtuHQayJcaa.VhF7FpC6IOuQkAf2B0o9WvVZOD \
iT_nf8w6D7mbgXoNKNYr6ARFq04L82PuVl40XmK4zCPvODVc5SmzNd31wam4KUTIBIur_rBO \
WRiMZg.d6TXijpD4YtxdtR8DITPjA"
                }
            }
        ],
        "aad": "BlrAXgMxBU2dw0RCbOchXGKyHN2mTQcoU3EKvnpfGl8",
        "iv": "oULwGExZbXZ431Pj0u9LxzfWg0hDb0tv",
        "tag": "EC0w0dBP1uaRyiksPF4l8w",
        "ciphertext": "nvq9PRIfNXOq8j8x"
}
```

Notice that the `spk` field under the recipient's field in the above 
example is a compact serialisation JWE representing a JWK of the sender
 (Bob). The clear text JWK is represented as follows:
```
{
       "kty": "OKP",
       "crv": "X25519",
       "x": "gmoy3KdDhRu-TyFE06cRZ58YA-wpqRueq3tiKyT8lyY"
}
```

Where `x` represents Alice's (sender) public key (ref keys table above).
 It is encrypted in a similar fashion as the outer payload but using a 
 new set of ephemeral keys. The JWE headers for this encryption will be:
```
{
    "typ": "jose",
    "cty": "jwk+json",
    "alg": "ECDH-ES+XC20PKW",
    "enc": "XC20P",
    "epk": {
        "kty": "OKP",
        "crv": "X25519",
        "x": "pp_8l9b7m2bs2AC0UpWyLo52qtwaqxS99CC3AM-KDTo"
    },
    "iv": "kISrzz67Jh9MX56FdW0behVLaIp2E3MY",
    "tag": "iZy_Tto0QSuJg2KPkN6nMQ"
}
```

Where `x` here is the epk used for the encryption along with `iv`. 
The output is the full value of `spk` in the outer envelope above.