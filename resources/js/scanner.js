const barcodeScanner = new Dynamsoft.BarcodeScanner({
  
  license: "DLS2eyJoYW5kc2hha2VDb2RlIjoiMTA0Njc1ODAyLU1UQTBOamMxT0RBeUxYZGxZaTFVY21saGJGQnliMm8iLCJtYWluU2VydmVyVVJMIjoiaHR0cHM6Ly9tZGxzLmR5bmFtc29mdG9ubGluZS5jb20iLCJvcmdhbml6YXRpb25JRCI6IjEwNDY3NTgwMiIsInN0YW5kYnlTZXJ2ZXJVUkwiOiJodHRwczovL3NkbHMuZHluYW1zb2Z0b25saW5lLmNvbSIsImNoZWNrQ29kZSI6LTE5NDg1MDM5MzV9",
  barcodeFormats:[Dynamsoft.DBR.EnumBarcodeFormat.BF_EAN_13 , Dynamsoft.DBR.EnumBarcodeFormat.BF_EAN_8 , Dynamsoft.DBR.EnumBarcodeFormat.BF_UPC_A],
});
(async () => {
  // Launch the scanner and wait for the result
  const result = await barcodeScanner.launch();
  // Display the first detected barcode's text in an alert
  if (result.barcodeResults.length) {
      alert(result.barcodeResults[0].text);
  }
})();