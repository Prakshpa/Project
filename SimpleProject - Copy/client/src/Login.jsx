import { useState } from "react";
import axios from "axios";
function Login() {
    const [input, setInput]=useState("");
    const [pinput, setPinput]=useState("");
    const [perr, setPerr]=useState("");
    const [err, setErr]=useState("");
    const [uerr, setUerr]=useState("");

    const sendMessage=async (e)=>{
        e.preventDefault();
        if(!input.trim() || !pinput.trim()) {
            setErr("Enter login credentials\n");
            setPerr("Password required\n");
            setUerr("Email or mobile no required\n");
            console.log("Hello");
            return;
        }
        try{
            const response=await axios.post("http://localhost:5000/home",{
                uId : input,
                pw : pinput,
            });
            try{
                const reply=response.data.reply;
                if(reply=="Sign up successful" || reply=="Login successful") {
                    document.cookie="user="+response.data.login;
                    location.href=`http://localhost:5173/home.html?a=${response.data.login}`;
                }else{
                    setErr(response.data.reply);
                    setUerr(response.data.uerr);
                    setPerr(response.data.perr);
                }
            }catch(e){
                console.log("Error in redirecting "+e);
            }
        }catch(e){
            setErr(e.response.data.reply);
            setPerr(e.response.data.perr);
            setUerr(e.response.data.uerr);
            console.log(err);
            console.log("Error communicating with server" + e);
        }
    }
    return (
    <div className="items-center border-rounded-lg shadow-lg bg-green-100 min-w-[300px] mx-auto lg:w-[50%] mt-10">
        <span className="text-red-900 text-xl">{err}{err? <br />:""}</span>
        <form onSubmit={sendMessage} method="post">
            <div className="bg-blue-800 text-2xl font-bold text-gray-400 my-4 gap-4">Login Page</div>
            <label htmlFor="login" className="gap-4 mr-10">User ID: </label>
            <input id="login" type="text" value={input} className="border p-2 rounded-lg w-[50%] bg-white" onChange={(e)=>setInput(e.target.value)} name="login" />
            <span className="text-red-600"><br/>{uerr}{uerr? <br />: uerr}</span>
            <label htmlFor="password" className="gap-4 mr-7">Password: </label>
            <input id="password" type="password" className="border p-2 rounded-lg w-[50%] mt-4 bg-white" value={pinput} onChange={(e)=>setPinput(e.target.value)} name="password" /><br />
            <span className="text-red-500">{perr}<br /></span>
            <button className="bg-blue-800 text-white p-2 rounded-lg mt-4 w-50 hover:bg-red-600" type="submit">Sign Up</button>
        </form>
    </div>
    );
}
export default Login