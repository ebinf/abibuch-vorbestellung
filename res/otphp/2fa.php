<?php
    define("TFA_DIGEST", "sha1");
    define("TFA_DIGITS", 6);
    define("TFA_PERIOD", 30);
    define("TFA_CIPHER", "AES-128-CBC");
    define("TFA_SECRET_LENGTH", 34);
    define("TFA_BACKUPCODE_QUANTITY", 4);
    define("TFA_BACKUPCODE_LENGTH", 12);
    define("TFA_BACKUPCODE_SEPARATOR", 6);

    require(RESDIRABS . "/res/assert/Assert.php");
    require(RESDIRABS . "/res/assert/Assertion.php");
    require(RESDIRABS . "/res/paragonie/EncoderInterface.php");
    require(RESDIRABS . "/res/paragonie/Binary.php");
    require(RESDIRABS . "/res/paragonie/Base32.php");
    require(RESDIRABS . "/res/otphp/Factory.php");
    require(RESDIRABS . "/res/otphp/ParameterTrait.php");
    require(RESDIRABS . "/res/otphp/OTPInterface.php");
    require(RESDIRABS . "/res/otphp/OTP.php");
    require(RESDIRABS . "/res/otphp/TOTPInterface.php");
    require(RESDIRABS . "/res/otphp/TOTP.php");
?>
