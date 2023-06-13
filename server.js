const express = require('express');
const app = express();
const http = require('http');
const server = http.createServer(app);
var sphp = require('sphp');
var path = require('path')
const fs = require('fs');
var bodyParser = require('body-parser')
app.use(express.static(path.join(__dirname, 'frontend')));
// app.use(sphp.express(path.join(__dirname, 'frontend')));
app.use( bodyParser.json() ); 
// app.get('/', (req, res) => {
//     res.sendFile(__dirname + '/frontend/');
// });
const chatsFolder = './chats/';
app.post('/savetofile', (req, res) => {
    var filename = req.body.filename
    var contents = req.body.contents
    fs.writeFileSync(chatsFolder+filename+".json", JSON.stringify(contents));
    res.send("success")
});
app.get('/getchatnames', (req, res) => {
    names = []
    console.log("here")
    fs.readdir(chatsFolder, (err, files) => {
        files.forEach(file => {
          console.log(file);
          names.push(file)
        });
        console.log(names)
        res.send(JSON.stringify(names))
      });
    
});
app.get('/getchat', (req, res) => {
    var chatname = req.body.chatname
    var obj = JSON.parse(fs.readFileSync(chatsFolder+chatname+".json", 'utf8'));
    res.send(JSON.stringify(obj))
});



server.listen(3000, () => {
  console.log('listening on *:3000');
});