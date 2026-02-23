import React from 'react';
import { createRoot } from 'react-dom/client';
import AdminDashboard from './Pages/admin/AdminDashboard';
import "../css/app.css";

const el = document.getElementById('root');
const root = createRoot(el);
root.render(<AdminDashboard />);
