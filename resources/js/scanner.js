// Inicializo la licencia del scanner de barras
Dynamsoft.License.LicenseManager.initLicense("DLS2eyJoYW5kc2hha2VDb2RlIjoiMTA0Njc1ODAyLU1UQTBOamMxT0RBeUxYZGxZaTFVY21saGJGQnliMm8iLCJtYWluU2VydmVyVVJMIjoiaHR0cHM6Ly9tZGxzLmR5bmFtc29mdG9ubGluZS5jb20iLCJvcmdhbml6YXRpb25JRCI6IjEwNDY3NTgwMiIsInN0YW5kYnlTZXJ2ZXJVUkwiOiJodHRwczovL3NkbHMuZHluYW1zb2Z0b25saW5lLmNvbSIsImNoZWNrQ29kZSI6LTE5NDg1MDM5MzV9");
/* Si llegasemos a usar vue tendriamos que poner la ruta de los archivos wasm, worker, etc
Dynamsoft.Core.CoreModule.engineResourcePaths.rootDirectory = "https://cdn.jsdelivr.net/npm/";
*/
// Cargar el wasm por adelantado para reducir el tiempo de carga
Dynamsoft.Core.CoreModule.loadWasm();

let pCvRouter = null; //Helper para evitar que se cree una nueva instancia de cvRouter cada vez que se ejecuta la función
let cvRouter = null; 

(async () => {
    cvRouter = await (pCvRouter = pCvRouter || Dynamsoft.CVR.CaptureVisionRouter.createInstance());
    let cameraView = await Dynamsoft.DCE.CameraView.createInstance();
    let cameraEnhancer = await Dynamsoft.DCE.CameraEnhancer.createInstance(cameraView);
    cameraEnhancer.setScanRegion({
      x: 25,
      y: 25,
      width: 50,
      height: 50,
      isMeasuredInPercentage: true,
    });
    document.querySelector("#camera-view-container").append(cameraView.getUIElement());
    cvRouter.setInput(cameraEnhancer);

    const resultsContainer = document.querySelector("#results");
    cvRouter.addResultReceiver({
      onDecodedBarcodesReceived: async (result) => {
        if (result.barcodeResultItems?.length) {
          resultsContainer.textContent = '';
          Dynamsoft.DCE.Feedback.beep();
    
          for (let item of result.barcodeResultItems) {
            const codigo = item.text.trim();
            resultsContainer.textContent += `${item.formatString}: ${codigo}\n\n`;
    
            try {
              const response = await fetch('/escaner/procesar', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ codigo })
              });
              const data = await response.json();
    
              if (response.ok) {
                resultsContainer.textContent += `✅ Código encontrado: ${data.name || 'sin nombre'}`;
              } else {
                resultsContainer.textContent += `❌ Código no encontrado: ${codigo}`;
              }
    
            } catch (err) {
              resultsContainer.textContent += `❌ Error al consultar el backend: ${err}`;
            }
          }
        }
      }
    });

    let filter = new Dynamsoft.Utility.MultiFrameResultCrossFilter();
    filter.enableResultCrossVerification("barcode", true);
    filter.enableResultDeduplication("barcode", true);
    await cvRouter.addResultFilter(filter);

    await cameraEnhancer.open();
    /*
    * ReadSingleBarcode para leer rapidamente un solo codigo de barras
    * ReadDistinctBarcodes para leer codigos de barras desde lejos    
    */
    let settings = await cvRouter.getSimplifiedSettings("ReadSingleBarcode");
    settings.barcodeSettings.barcodeFormatIds = Dynamsoft.DBR.EnumBarcodeFormat.BF_EAN_13 | Dynamsoft.DBR.EnumBarcodeFormat.BF_EAN_8;
    await cvRouter.updateSettings("ReadSingleBarcode", settings);
    await cvRouter.startCapturing("ReadSingleBarcode");
  })();