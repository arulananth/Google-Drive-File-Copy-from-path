<!DOCTYPE html>
<html>
  <head>
    <title>Drive API Quickstart</title>
    <meta charset="utf-8" />
    <style type="text/css">
      
      .btnSquareOutline {
    background-color: white;
    border: 2px solid #E8E8E8;
    border-radius: 5px;
    color: #383838;
    font-size: 15px;
    font-weight: lighter;
    padding: 5px 5px;
    width: 75px;
    height: 75px;
}

.btnSquareOutline > i {
    font-size: 24px;
}

    </style>
  </head>
  <body>
    <p>Drive API Quickstart</p>
<script src="https://apis.google.com/js/api.js"></script>
<script>
  /**
   * Sample JavaScript code for drive.files.insert
   * See instructions for running APIs Explorer code samples locally:
   * https://developers.google.com/explorer-help/guides/code_samples#javascript
   */

  function authenticate() {
    return gapi.auth2.getAuthInstance()
        .signIn({scope: "https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/drive.appdata https://www.googleapis.com/auth/drive.apps.readonly https://www.googleapis.com/auth/drive.file"})
        .then(function() { console.log("Sign-in successful"); },
              function(err) { console.error("Error signing in", err); });
  }
  function loadClient() {
    return gapi.client.load("https://content.googleapis.com/discovery/v1/apis/drive/v2/rest")
        .then(function() { 

          console.log("GAPI client loaded for API");
          insertFile("assets/pdf/pdf.pdf",makeMsg) },
              function(err) { console.error("Error loading GAPI client for API", err); });
  }
  // Make sure the client is loaded and sign-in is complete before calling this method.
  /**
 * Insert new file.
 *
 * @param {File} fileData File object to read data from.
 * @param {Function} callback Function to call when the request is complete.
 */
function insertFile(file, callback) {

  var fileData =null;
  var xhr = new XMLHttpRequest(); 
  xhr.open("GET", file);
  xhr.responseType = "blob";
  const boundary = '-------314159265358979323846';
  const delimiter = "\r\n--" + boundary + "\r\n";
  const close_delim = "\r\n--" + boundary + "--";
  xhr.onload = function() 
  {
    blob = xhr.response;//xhr.response is now a blob object
  
  var reader = new FileReader();
  reader.readAsBinaryString(blob);
  reader.onload = function(e) {
    
    var metadata = {
      'title':"Save to drive ",
      'mimeType': "application/pdf"
    };
    var contentType ="application/pdf"
    var base64Data = btoa(reader.result);
    var multipartRequestBody =
        delimiter +
        'Content-Type: application/json\r\n\r\n' +
        JSON.stringify(metadata) +
        delimiter +
        'Content-Type: ' + contentType + '\r\n' +
        'Content-Transfer-Encoding: base64\r\n' +
        '\r\n' +
        base64Data +
        close_delim;

    var request = gapi.client.request({
        'path': '/upload/drive/v2/files',
        'method': 'POST',
        'params': {'uploadType': 'multipart'},
        'headers': {
          'Content-Type': 'multipart/mixed; boundary="' + boundary + '"'
        },
        'body': multipartRequestBody});
    if (!callback) {
      callback = function(file) {
        console.log(file)
      };
    }
    request.execute(callback,file);
   }
  }
xhr.send();
}
function makeMsg(file){
  console.log(file)
  file.downloadlink
  $("#success-msg a").attr("href",file.downloadlink);
  $("#success-msg").fadeIn();
}
  function execute() {
    return gapi.client.drive.files.insert({
      "resource": {}
    })
        .then(function(response) {
                // Handle the results here (response.result has the parsed body).
                console.log("Response", response);
              },
              function(err) { console.error("Execute error", err); });
  }
  gapi.load("client:auth2", function() {
    gapi.auth2.init({client_id: "830217855345-ma65hpad38ki0edbmlr21l3b34iqsie4.apps.googleusercontent.com"});
  });
</script>
<button class="btnSquareOutline" onclick="authenticate().then(loadClient)">Save File</button>
<div id="success-msg" style="display:none" class="alert alert-success"><a href="" id="" /></div>
</body>
</html>