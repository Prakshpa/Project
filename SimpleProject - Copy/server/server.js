const express=require('express');
const mysql=require("mysql2/promise");
const cors=require('cors');
const dotenv=require('dotenv');
const cookieParser=require('cookie-parser');
dotenv.config();
const app=express();
app.use(cors());
app.use(express.json());
app.use(cookieParser(process.env.COOKIE_SECRET));
const bcrypt=require("bcrypt");
const pool=mysql.createPool({
    host:"localhost",
    user:"root",
    password:"",
    database:"ecommerce",
    waitForConnections:true,
    connectionLimit:10,
    queueLimit:0,
});
let user="abc", pp="delete.jpg", fullName="Full name";
app.post("/find", async (req,res)=>{
    try{
        var validity="Not valid";
        if(user==req.body.user) validity="";
        res.json({pp:pp, validity:validity, uName:fullName});
    }catch(e){
        console.log(e);
        res.json({pp:"Capture.jpg", validity:"Internal server error"});
    }
})
app.post("/home", async (req, res)=>{
    const {uId, pw}=req.body;
    try {
        var uvalue=filter(uId);
        var returnVal ="A problem occured";
        var uidErr="*";
        var pwErr="*";
        if(!uvalue){
            returnVal="Email or phone required";
            uidErr="Email or phone required";
        }else if(uvalue.length>5){
            if(/^[A-Za-z]/.test(uvalue)){
                if(uvalue.indexOf("@")==-1 || uvalue.indexOf(".")==-1) uidErr="Invalid Login";
                if(/[^a-z0-9\@\.]/.test(uvalue)) returnVal="Invalid email";
                else if(uvalue.indexOf("@")!=uvalue.lastIndexOf("@")) uidErr="Invalid email @";
                else if(uvalue.indexOf("@")+2>uvalue.lastIndexOf(".") || uvalue[uvalue.indexOf("@")-1]==".") uidErr="Invalid email";
                else{
                    var host=uvalue.substring(uvalue.indexOf("@")+1, uvalue.length);
                    if(/^[_.]/i.test(host)){
                        uidErr="Invalid email alphabet: "+host;
                    }else{
                        var end=host.substring(host.lastIndexOf(".")+1, host.length);
                        if(end.length<2 || end.length>6) uidErr="Invalid email end";
                        else if(/[^a-z]/i.test(end)) uidErr="Invalid email end";
                    }
                    
                }
            }else if(/^[0-9]$/.test(uvalue)){
                if(uvalue<9700000000 || uvalue>9899999999){
                    uidErr="Mobile number should be 10 digit number starting from 97 or 98";
                    returnVal="Invalid mobile number";
                }
            }else{
                uidErr="Enter email or mobile no.";
                returnVal="Email your email address or Mobile number for User ID ";
            }
        }else{
            uidErr="Invalid Email or mobile no";
            returnVal="Enter valid email or phone for UserId";
        }
        if(uidErr==pwErr){
            if(pw.length<8) pwErr="Password must be at least 8 characters";
            else if(/[^a-zA-Z0-9\!\@\#\$\%\^\&\*]/.test(pw)) pwErr="Password can only contain letters, numbers and !@#$%^&*";
            else if(!/[a-z]/.test(pw)) pwErr="Password must contain at least one lowercase letter";
            else if(!/[A-Z]/.test(pw)) pwErr="Password must contain at least one uppercase letter";
            else if(!/[0-9]/.test(pw)) pwErr="Password must contain at least one number";
            else if(!/[\!\@\#\$\%\^\&\*]/.test(pw)) pwErr="Password must contain at least one special character";
            if(pwErr!="*") returnVal="Invalid password";
            else{
                try{
                    var params=[uvalue,"react"];
                    const [rows]=await pool.execute("SELECT * FROM users WHERE login=? AND role=?", params);
                    if(rows.length>0) {
                        if(await bcrypt.compare(pw, rows[0].password)){
                            user=uvalue;
                            pp=rows[0].PP;
                            fullName=rows[0]['Full name'];
                            returnVal="Login successful";
                            res.cookie("user", uId, {expires:new Date(Date.now()+60000), path:"/home.html"});
                        }else {
                            pwErr="Incorrect password";
                            returnVal="Invalid login credentials";
                        }
                    }else {
                        var bpw=await bcrypt.hash(pw,10);
                        params=[uvalue, bpw, "react"];
                        var result=await pool.execute("Insert into users (login, password, role) VALUES (?, ?, ?)", params);
                        if(result)returnVal="Sign up successful";                                    
                        user=uvalue;
                        res.cookie("user", uId, {expires:new Date(Date.now()+60000), httpOnly:true});

                    }
                }catch(err){
                    returnVal="Error communicating with database";
                }
                res.json({reply:returnVal, login:uvalue})
            }
            if(pwErr) res.json({reply:returnVal, uerr:uidErr, perr:pwErr});
        }else {
            console.log("UID: "+uvalue+", Password: "+pw);
            returnVal="Invalid login credentials";
            try{
                res.json({reply:returnVal, uerr:uidErr, perr:pwErr});
            }catch(e){
                console.log("Error in sending response"+e);
            }
        }
    } catch (error) {
        console.log("Error in filter function"+error);
         returnVal="Internal server error";
    }
    console.log(returnVal);
});
function filter(value){  
    // var validity=/^[a-zA-Z0-9_ \-\'\.\,]+$/;
    var numChar=/^[a-zA-Z0-9]/;
    var numCharI=/[a-zA-Z0-9]$/;
    try {
        if(!value) return value;          
        else{
            value=(value.toLowerCase()).trim();
            value=value.replace(/[^a-zA-Z0-9_\.\@]/, "");
            value=value.trim();
        }
        while(!numChar.test(value) && value.length>=1) {
            value=value.substring(1);
            if(value=='') return value;
        }
        while(!numCharI.test(value) && value!="") {
            value=value.substring(0, value.length-1);
            // if(value[])
            if(value=="") return value;
        }
        value=value.replace(/[\.]{2,}/, '.');
        value=value.replace(/[\@]{2,}/, "@");
        value=value.replace("[\.\@]{2,}","@");
        
    } catch (error) {
        console.log("filter err"+error);
        return "";
    }
    return value;
}
const PORT = process.env.PORT || 5000;
app.listen(PORT, ()=>console.log("Listening to port "+PORT));