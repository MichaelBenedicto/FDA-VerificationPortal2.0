import React from 'react';
import { createRoot } from 'react-dom/client';
import AdminLogin from './Pages/admin/AdminLogin';
import "../css/app.css";


const el = document.getElementById('root');
const root = createRoot(el);
root.render(<AdminLogin />);


