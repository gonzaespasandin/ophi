// Inicializo la licencia del scanner de barras
Dynamsoft.License.LicenseManager.initLicense("DLS2eyJoYW5kc2hha2VDb2RlIjoiMTA0Njc1ODAyLU1UQTBOamMxT0RBeUxYZGxZaTFVY21saGJGQnliMm8iLCJtYWluU2VydmVyVVJMIjoiaHR0cHM6Ly9tZGxzLmR5bmFtc29mdG9ubGluZS5jb20iLCJvcmdhbml6YXRpb25JRCI6IjEwNDY3NTgwMiIsInN0YW5kYnlTZXJ2ZXJVUkwiOiJodHRwczovL3NkbHMuZHluYW1zb2Z0b25saW5lLmNvbSIsImNoZWNrQ29kZSI6LTE5NDg1MDM5MzV9");

// Cargar el wasm por adelantado para reducir el tiempo de carga
Dynamsoft.Core.CoreModule.loadWasm();

(async () => {
    let cvRouter = await Dynamsoft.CVR.CaptureVisionRouter.createInstance();
    let cameraView = await Dynamsoft.DCE.CameraView.createInstance();
    let cameraEnhancer = await Dynamsoft.DCE.CameraEnhancer.createInstance(cameraView);
    document.querySelector("#camera-view-container").append(cameraView.getUIElement());
    cvRouter.setInput(cameraEnhancer);

    const resultsContainer = document.querySelector("#results");
    cvRouter.addResultReceiver({ onDecodedBarcodesReceived: (result) => {
      if (result.barcodeResultItems?.length) {
        resultsContainer.textContent = '';
        for (let item of result.barcodeResultItems) {
          resultsContainer.textContent += `${item.formatString}: ${item.text}\n\n`;
        }
      }
    }});

    let filter = new Dynamsoft.Utility.MultiFrameResultCrossFilter();
    filter.enableResultCrossVerification("barcode", true);
    filter.enableResultDeduplication("barcode", true);
    await cvRouter.addResultFilter(filter);

    await cameraEnhancer.open();
    await cvRouter.startCapturing("ReadSingleBarcode");
  })();