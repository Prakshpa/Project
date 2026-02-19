import '../index.css'
import Login from '../Login.jsx'
import App from '../App.jsx'
import {BrowserRouter,Routes, Route} from "react-router-dom";
function Router(){
    return (
    <BrowserRouter>
      <Routes>
        <Route path='/' element={<Login />} />
        <Route path="/login" element={<Login />} />
        <Route path="/home" element={<App />} />
        <Route path='' element={<Login />} />
        <Route path='/home.html' element={<App />} />
      </Routes>
    </BrowserRouter>
    )
}
export default Router