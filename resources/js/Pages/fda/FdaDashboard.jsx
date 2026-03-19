import React, { useEffect, useState } from "react";
import axios from "axios";
import FdaHRTable from "./FdaHRTable";
import { 
  Menu, 
  X, 
  ChevronLeft, 
  ChevronRight,
  LogOut,
  Users,
  FileText,
  Database,
  Settings,
  Home
} from "lucide-react";

export default function FdaDashboard() {
  const [user, setUser] = useState(null);
  const [activePage, setActivePage] = useState(null);
  const [collapsed, setCollapsed] = useState(false);
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  useEffect(() => {
  axios
    .get("/fda/user") // Changed from /admin/user
    .then((res) => {
      setUser(res.data.user);
    })
    .catch(() => (window.location.href = "/fda/login")); // Changed from /admin/login
}, []);

  // Icon mapping for menu items
  const getIcon = (key) => {
    const icons = {
      hr: Users,
      cchuhsrr: FileText,
      cdrr: Database,
      cdrrhr: Database,
      cfrr: FileText,
      csl: FileText,
      adminusers: Settings,
    };
    const IconComponent = icons[key] || Home;
    return <IconComponent size={20} />;
  };

  // Sidebar menu based on user_level
  const getMenuItems = () => {
    if (!user) return [];

    if (user.user_level === -1) {
      return [
        { key: "hr", label: "HRDD" },
        { key: "cchuhsrr", label: "CCHUHSRR" },
        { key: "cdrr", label: "CDRR" },
        { key: "cdrrhr", label: "CDRRHR" },
        { key: "cfrr", label: "CFRR" },
        { key: "csl", label: "CSL" },
        { key: "adminusers", label: "Admin Users" },
      ];
    }

    if (user.user_level === 1) return [{ key: "hr", label: "HRDD" }];
    if (user.user_level === 2) return [{ key: "cchuhsrr", label: "CCHUHSRR" }];
    if (user.user_level === 3) return [{ key: "cdrr", label: "CDRR" }];
    if (user.user_level === 4) return [{ key: "cdrrhr", label: "CDRRHR" }];
    if (user.user_level === 5) return [{ key: "cfrr", label: "CFRR" }];
    if (user.user_level === 6) return [{ key: "csl", label: "CSL" }];

    return [];
  };

  const menuItems = getMenuItems();

  // Auto-load first tab based on access
  useEffect(() => {
    if (menuItems.length > 0 && activePage === null) {
      setActivePage(menuItems[0].key);
    }
  }, [menuItems]);

  const handleLogout = async () => {
  const confirmLogout = window.confirm("Are you sure you want to logout?");
  if (!confirmLogout) return;

  try {
    await axios.post("/fda/logout"); // Changed from /admin/logout
    window.location.href = "/fda/login"; // Changed from /admin/login
  } catch (err) {
    console.error("Logout failed");
  }
};

  if (!user) {
    return (
      <div className="h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
        <div className="bg-white p-8 rounded-2xl shadow-lg w-96 text-center border border-gray-200">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-[#00bf63] mx-auto mb-4"></div>
          <h2 className="text-xl font-bold text-[#286634]">Loading...</h2>
          <p className="text-gray-500 mt-2">Fetching admin session...</p>
        </div>
      </div>
    );
  }

  const renderContent = () => {
    switch (activePage) {
      case "hr":
        return (
          <div className="space-y-6">
            <div className="flex items-center justify-between">
              <div>
                <h2 className="text-3xl font-bold text-[#286634]">FDA Employees</h2>
                <p className="text-gray-600 mt-1">
                  Manage and view FDA employee records
                </p>
              </div>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
              <div className="p-6">
                <FdaHRTable />
              </div>
            </div>
          </div>
        );

      case "cchuhsrr":
        return (
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl font-bold text-[#286634]">CCHUHSRR</h2>
              <p className="text-gray-600 mt-1">Center for Clinical and Health Use of Health Services Research Records</p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
              <p className="text-gray-500 text-center">[CCHUHSRR DataTable Component Here]</p>
            </div>
          </div>
        );

      case "cdrr":
        return (
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl font-bold text-[#286634]">CDRR</h2>
              <p className="text-gray-600 mt-1">Clinical Data Research Records</p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
              <p className="text-gray-500 text-center">[CDRR DataTable Component Here]</p>
            </div>
          </div>
        );

      case "cdrrhr":
        return (
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl font-bold text-[#286634]">CDRRHR</h2>
              <p className="text-gray-600 mt-1">Clinical Data Research Records - HR</p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
              <p className="text-gray-500 text-center">[CDRRHR DataTable Component Here]</p>
            </div>
          </div>
        );

      case "cfrr":
        return (
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl font-bold text-[#286634]">CFRR</h2>
              <p className="text-gray-600 mt-1">Clinical and Financial Research Records</p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
              <p className="text-gray-500 text-center">[CFRR DataTable Component Here]</p>
            </div>
          </div>
        );

      case "csl":
        return (
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl font-bold text-[#286634]">CSL</h2>
              <p className="text-gray-600 mt-1">Clinical Services Log</p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
              <p className="text-gray-500 text-center">[CSL DataTable Component Here]</p>
            </div>
          </div>
        );

      case "adminusers":
        return (
          <div className="space-y-6">
            <div>
              <h2 className="text-3xl font-bold text-[#286634]">Admin Users</h2>
              <p className="text-gray-600 mt-1">Manage administrative user accounts</p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
              <p className="text-gray-500 text-center">[Admin Users Table Here]</p>
            </div>
          </div>
        );

      default:
        return (
          <div className="bg-yellow-50 border border-yellow-200 text-yellow-800 p-6 rounded-2xl">
            <p className="font-semibold">No module selected.</p>
            <p className="text-sm mt-1">Please select a module from the sidebar.</p>
          </div>
        );
    }
  };

  return (
    <div className="min-h-screen flex bg-gradient-to-br from-gray-50 to-gray-100">
      {/* Mobile Menu Button */}
      <button
        onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
        className="lg:hidden fixed top-4 left-4 z-50 p-2 bg-[#286634] text-white rounded-lg shadow-lg"
      >
        {mobileMenuOpen ? <X size={24} /> : <Menu size={24} />}
      </button>

      {/* Overlay for mobile */}
      {mobileMenuOpen && (
        <div
          className="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"
          onClick={() => setMobileMenuOpen(false)}
        />
      )}

      {/* Sidebar */}
      <div
        className={`
          fixed lg:sticky top-0 h-screen
          ${collapsed ? "lg:w-20" : "lg:w-72"}
          ${mobileMenuOpen ? "translate-x-0" : "-translate-x-full lg:translate-x-0"}
          w-72 bg-gradient-to-b from-[#286634] to-[#1f4d27] text-white 
          flex flex-col shadow-2xl transition-all duration-300 ease-in-out z-40
        `}
      >
        {/* Header */}
        <div className="p-5 border-b border-green-900/30">
          <div className="flex items-center justify-between">
            {!collapsed && (
              <div className="flex items-center space-x-3">
                
                <h1 className="text-lg font-bold tracking-wide">
                 FDA Admin Portal
                </h1>
              </div>
            )}
            {collapsed && (
              <div className="w-10 h-10 bg-[#00bf63] rounded-lg flex items-center justify-center mx-auto">
                <span className="text-white font-bold text-lg">FDA</span>
              </div>
            )}
          </div>

          {/* User Info */}
          {!collapsed && (
            <div className="mt-4 bg-[#1f4d27] p-4 rounded-xl border border-green-900/30 shadow-lg">
              <div className="flex items-center space-x-3">
                <div className="w-10 h-10 bg-[#00bf63] rounded-full flex items-center justify-center flex-shrink-0">
                  <span className="text-white font-semibold text-sm">
                    {user.fullName?.charAt(0)?.toUpperCase() || "U"}
                  </span>
                </div>
                <div className="flex-1 min-w-0">
                  <p className="text-sm font-semibold text-white truncate">
                    {user.fullName}
                  </p>
                  <p className="text-xs text-green-200 truncate">
                    {user["center/office"]}
                  </p>
                  <p className="text-xs text-green-300 truncate">
                    @{user.userName}
                  </p>
                </div>
              </div>
            </div>
          )}
        </div>

        {/* Menu Items */}
        <div className="flex-1 p-3 space-y-1 overflow-y-auto">
          {menuItems.map((item) => (
            <button
              key={item.key}
              onClick={() => {
                setActivePage(item.key);
                setMobileMenuOpen(false);
              }}
              className={`
                w-full flex items-center space-x-3 px-4 py-3 rounded-xl 
                font-semibold transition-all duration-200
                ${
                  activePage === item.key
                    ? "bg-[#00bf63] text-white shadow-lg scale-105"
                    : "hover:bg-[#79af60] hover:scale-102"
                }
                ${collapsed ? "justify-center" : ""}
              `}
              title={collapsed ? item.label : ""}
            >
              <span className={activePage === item.key ? "text-white" : "text-green-100"}>
                {getIcon(item.key)}
              </span>
              {!collapsed && <span>{item.label}</span>}
            </button>
          ))}
        </div>

        {/* Footer */}
        <div className="p-3 border-t border-green-900/30 space-y-2">
          {/* Collapse Toggle (Desktop Only) */}
          <button
            onClick={() => setCollapsed(!collapsed)}
            className="hidden lg:flex w-full items-center justify-center space-x-2 px-4 py-3 rounded-xl font-semibold hover:bg-[#79af60] transition-all duration-200"
          >
            {collapsed ? <ChevronRight size={20} /> : <ChevronLeft size={20} />}
            {!collapsed && <span>Collapse</span>}
          </button>

          {/* Logout Button */}
          <button
            onClick={handleLogout}
            className={`
              w-full flex items-center space-x-3 px-4 py-3 rounded-xl 
              font-semibold bg-red-600 hover:bg-red-700 transition-all duration-200
              ${collapsed ? "justify-center" : ""}
            `}
            title={collapsed ? "Logout" : ""}
          >
            <LogOut size={20} />
            {!collapsed && <span>Logout</span>}
          </button>
        </div>
      </div>

      {/* Main Content */}
      <div className="flex-1 overflow-auto">
        <div className="p-4 lg:p-8 max-w-7xl mx-auto">
          {/* Content Area */}
          {renderContent()}
        </div>
      </div>
    </div>
  );
}
