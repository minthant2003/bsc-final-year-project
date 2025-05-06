window.startBarcodeScan = () => {
    // Initialize Quagga scanner
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.getElementById("barcodeScanner"),
            constraints: {
                facingMode: "environment" // back camera
            }
        },
        decoder: {
            readers: ["code_128_reader"] // code format
        },
        locate: false
    }, function(error) {
        if (error) {
            console.error("Error initializing Quagga:", error);
            return;
        }
        Quagga.start();
    });

    const detectedEvent = (result) => {
        const code = result.codeResult.code;
        // beep sound
        const beep = new Audio("https://actions.google.com/sounds/v1/alarms/beep_short.ogg");
        beep.play();

        // console.log("detected : ", result);
        validateScannedCode(code);

        // stop & remove event listener
        Quagga.stop();
        Quagga.offDetected(detectedEvent);
    };

    // Attach event listener
    Quagga.onDetected(detectedEvent);
}