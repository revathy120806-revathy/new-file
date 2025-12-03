import express from 'express'
import path from 'path'
import { fileURLToPath } from 'url';
import http from 'http'
import https from 'https'



const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app=express()
app.set("view engine","ejs")
const pathName=path.resolve('assets')
const imgPath=path.resolve(__dirname)
const folderName='img'
//app.use(express.static(path.join(pathName,folderName)))
app.use('/node_modules', express.static(path.join(__dirname, 'node_modules')))


app.get("/",(req,resp)=>{
 const absPath=path.resolve('index.html')
resp.sendFile(absPath)
console.log(absPath)

app.use(express.static(imgPath))
console.log(imgPath)



})


app.get("/contact.html",(req,resp)=>{
const absPath=path.resolve('contact.html')
resp.sendFile(absPath)

})


app.get("/resume.html",(req,resp)=>{
const absPath=path.resolve('resume.html')
resp.sendFile(absPath)


})


app.get("/about.html",(req,resp)=>{
const absPath=path.resolve('about.html')
resp.sendFile(absPath)

})

app.get("/index.html",(req,resp)=>{
const absPath=path.resolve('index.html')
resp.sendFile(absPath)


})

app.get('/download/resume', (req, res) => {
  const remoteUrl = 'https://1drv.ms/b/c/c365a2806ea20738/IQDeFD_SWbDjRKszsEuzCmTVAatFek3NxlkKv4MoccJSTms?e=dxvw6y' // <- put remote resume URL here
  // 302 redirect to remote file (browser will download/open it)
  res.redirect(remoteUrl)
})

app.get('/download/resume', (req, res) => {
  const remoteUrl = 'https://example.com/path/to/resume.pdf' // <- put remote resume URL here
  const urlObj = new URL(remoteUrl)
  const client = urlObj.protocol === 'https:' ? https : http

  client.get(urlObj, (remoteRes) => {
    if (remoteRes.statusCode !== 200) {
      res.status(remoteRes.statusCode).send('Remote file not available')
      return
    }
    res.setHeader('Content-Disposition', 'attachment; filename="Resume.pdf"')
    res.setHeader('Content-Type', remoteRes.headers['content-type'] || 'application/pdf')
    remoteRes.pipe(res)
  }).on('error', (err) => {
    console.error('Resume proxy error:', err)
    res.status(500).send('Download error')
  })
})
// ...existing code...


app.listen(3200)