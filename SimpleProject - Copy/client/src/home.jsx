import './App.css'
import { createRoot } from 'react-dom/client'
import { StrictMode } from 'react'
import Router from './router/router'
createRoot(document.getElementById('root1')).render(
  <StrictMode >
    <Router />
  </StrictMode>,
)