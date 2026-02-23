import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router-dom";
import EmployeeView from "./Pages/admin/EmployeeView";
import "../css/app.css";

const el = document.getElementById("root");
const root = createRoot(el);

root.render(
  <BrowserRouter>
    <EmployeeView />
  </BrowserRouter>
);
