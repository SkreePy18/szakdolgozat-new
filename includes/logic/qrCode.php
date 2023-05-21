<?php
    require "../vendor/autoload.php";
    use Endroid\QrCode\QrCode;
    use Endroid\QrCode\Writer\PngWriter;

    function generateQRCode($token, $login_required) {
        if($token && $login_required == "true") {
            $qr = QrCode::create(BASE_URL . "tokens/redeemToken.php?token=" . $token);
            $writer = new PngWriter();
            $result = $writer->write($qr);
    
            return $result;
		} elseif($token && $login_required == "false") {
			$qr = QrCode::create(BASE_URL . "tokens/redeemApi.php?token=" . $token);
            $writer = new PngWriter();
            $result = $writer->write($qr);
			
			return $result;
		}
        
    }
?>