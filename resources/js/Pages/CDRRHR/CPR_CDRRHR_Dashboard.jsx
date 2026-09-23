import React, { useEffect, useState } from "react";
import axios from "axios";
import { Edit } from "lucide-react";

export default function CPR_CDRRHR_Dashboard() {
  const [records, setRecords] = useState([]);
  const [searchInput, setSearchInput] = useState("");
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);

  // Pagination states
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [total, setTotal] = useState(0);

  // Import Progress States
  const [isImporting, setIsImporting] = useState(false);
  const [importProgress, setImportProgress] = useState(0);
  const [importStatus, setImportStatus] = useState("");

  // For Add/Edit Modal
  const [modalOpen, setModalOpen] = useState(false);
  const [editData, setEditData] = useState(null);
  const [form, setForm] = useState({
    registration_number: "",
    product_name: "",
    manufacturer: "",
    country_of_origin: "",
    trader: "",
    distributor: "",
    issuance_date: "",
    expiry_date: "",
    is_canceled: "N",
  });

  // Validation errors state
  const [errors, setErrors] = useState({});

  // Notification state
  const [notification, setNotification] = useState({
    show: false,
    type: "", // 'success' or 'error'
    message: "",
  });

  // Auto-hide notification after 5 seconds
  useEffect(() => {
    if (notification.show) {
      const timer = setTimeout(() => {
        setNotification({ show: false, type: "", message: "" });
      }, 5000);
      return () => clearTimeout(timer);
    }
  }, [notification.show]);

  const showNotification = (type, message) => {
    setNotification({ show: true, type, message });
  };

  const fetchRecords = async (pageNumber = 1, searchValue = search) => {
    setLoading(true);
    try {
      const res = await axios.get("/fda/cdrrhr/cpr", {
        params: {
          search: searchValue,
          page: pageNumber,
        },
      });

      setRecords(res.data.data);
      setPage(res.data.current_page);
      setLastPage(res.data.last_page);
      setTotal(res.data.total);
    } catch (err) {
      console.error("Full Axios Error Object:", err);
      console.error("Backend Error Response:", err.response);

      const errorMsg = err.response?.data?.message || err.message || "Failed to load Medical Device CPR.";
      showNotification("error", errorMsg);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchRecords(1, search);
  }, [search]);

  const handleSearch = () => {
    setSearch(searchInput);
  };

  const handleFullList = () => {
    setSearchInput("");
    setSearch("");
  };

  const openAddModal = () => {
    setEditData(null);
    setForm({
      registration_number: "",
      product_name: "",
      manufacturer: "",
      country_of_origin: "",
      trader: "",
      distributor: "",
      issuance_date: "",
      expiry_date: "",
      is_canceled: "N",
    });
    setErrors({});
    setModalOpen(true);
  };

  const openEditModal = (row) => {
    setEditData(row);
    setForm({
      registration_number: row.registration_number,
      product_name: row.product_name || "",
      manufacturer: row.manufacturer || "",
      country_of_origin: row.country_of_origin || "",
      trader: row.trader || "",
      distributor: row.distributor || "",
      issuance_date: row.issuance_date || "",
      expiry_date: row.expiry_date || "",
      is_canceled: row.is_canceled || "N",
    });
    setErrors({});
    setModalOpen(true);
  };

  const handleImportChange = async (e) => {
    const file = e.target.files[0];
    if (!file) return;

    const allowedExtensions = /(\.csv|\.xlsx|\.xls)$/i;
    if (!allowedExtensions.exec(file.name)) {
      showNotification("error", "Please select a valid CSV or Excel file.");
      e.target.value = "";
      return;
    }

    if (window.confirm(`Are you sure you want to import data from ${file.name}?`)) {
      const formData = new FormData();
      formData.append("import_file", file);

      try {
        setIsImporting(true);
        setImportProgress(0);
        setImportStatus("Uploading file to server...");

        await axios.post("/fda/cdrrhr/cpr/import", formData, {
          headers: { "Content-Type": "multipart/form-data" },
          onUploadProgress: (progressEvent) => {
            const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
            setImportProgress(percentCompleted);
            if (percentCompleted === 100) {
              setImportStatus("Processing and updating records in database...");
            }
          },
        });

        showNotification("success", "File records successfully imported/updated!");
        fetchRecords(1, search);
      } catch (err) {
        console.error(err);
        showNotification("error", err.response?.data?.message || "Failed to process imported file.");
      } finally {
        setIsImporting(false);
        setImportProgress(0);
        setImportStatus("");
        e.target.value = "";
      }
    } else {
      e.target.value = "";
    }
  };

  const handleSubmit = async () => {
    let localErrors = {};
    if (!form.registration_number) localErrors.registration_number = "CPR Number is required";

    if (Object.keys(localErrors).length > 0) {
      setErrors(localErrors);
      return;
    }

    try {
      const confirmMsg = editData
        ? "Do you want to update this CPR Details?"
        : "Do you want to add this CPR Details?";

      if (!window.confirm(confirmMsg)) return;

      if (editData) {
        await axios.post(`/fda/cdrrhr/cpr/update/${editData.registration_number}`, form);
        showNotification("success", "CPR Details updated successfully!");
      } else {
        await axios.post("/fda/cdrrhr/cpr/add", form);
        showNotification("success", "Medical Device CPR added successfully!");
      }

      setTimeout(() => {
        setModalOpen(false);
        setErrors({});
      }, 500);

      fetchRecords(page, search);
    } catch (err) {
      console.error(err.response?.data || err.message);

      if (err.response?.data?.errors) {
        const formatFieldName = (fieldName) => {
          return fieldName
            .replace(/_/g, " ")
            .toLowerCase()
            .split(" ")
            .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
            .join(" ");
        };

        const errorList = Object.entries(err.response.data.errors)
          .map(([field, messages]) => {
            const formattedField = formatFieldName(field);
            return messages
              .map((msg) => msg.replace(/The\s+([a-z\s]+)\s+field/i, `The ${formattedField} field`))
              .join("\n");
          })
          .join("\n");

        showNotification("error", errorList);
      } else {
        showNotification("error", err.response?.data?.message || err.message || "An error occurred.");
      }
    }
  };

  return (
    <div>
      {/* Toast Notification */}
      {notification.show && (
        <div
          className={`fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
            notification.type === "success"
              ? "bg-green-100 border border-green-400 text-green-800"
              : "bg-red-100 border border-red-400 text-red-800"
          }`}
          style={{ animation: "slideInRight 0.3s ease-out" }}
        >
          <div className="flex items-start">
            <div className="flex-shrink-0">
              {notification.type === "success" ? (
                <svg className="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                </svg>
              ) : (
                <svg className="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414-1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                </svg>
              )}
            </div>
            <div className="ml-3 flex-1">
              <p className="text-sm font-medium whitespace-pre-line">{notification.message}</p>
            </div>
            <button
              onClick={() => setNotification({ show: false, type: "", message: "" })}
              className="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-600"
            >
              <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      )}

      {/* Bulk Import Progress Bar Modal */}
      {isImporting && (
        <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-60 z-50">
          <div className="bg-white p-6 rounded-xl shadow-2xl w-96 text-center">
            <h3 className="text-lg font-bold mb-3 text-gray-800">Importing Data</h3>
            
            <p className="text-sm text-gray-600 mb-4">{importStatus}</p>

            {/* Outer Progress Bar Container */}
            <div className="w-full bg-gray-200 rounded-full h-4 mb-2 overflow-hidden">
              {/* Inner Animated Progress Fill */}
              <div
                className="bg-orange-600 h-4 rounded-full transition-all duration-300 ease-out"
                style={{ width: `${importProgress}%` }}
              />
            </div>

            <p className="text-xs font-semibold text-gray-500 mb-4">{importProgress}% Completed</p>

            {/* Spinner for processing state */}
            {importProgress === 100 && (
              <div className="flex justify-center items-center gap-2 text-xs text-orange-600 font-medium">
                <svg className="animate-spin h-4 w-4 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Finalizing database upsert... Please do not close this window.</span>
              </div>
            )}
          </div>
        </div>
      )}

      <style>{`
        @keyframes slideInRight {
          from { transform: translateX(100%); opacity: 0; }
          to { transform: translateX(0); opacity: 1; }
        }
      `}</style>

      {/* Toolbar */}
      <div className="flex flex-wrap items-center mb-4 gap-2">
        <input
          type="text"
          placeholder="Search Medical Device CPR..."
          className="border p-2 rounded w-full md:w-1/3"
          value={searchInput}
          onChange={(e) => setSearchInput(e.target.value)}
        />

        <button onClick={handleSearch} className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          Search
        </button>
        <button onClick={handleFullList} className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800">
          Full List
        </button>

        <label className={`bg-orange-600 text-white px-4 py-2 rounded inline-flex items-center ${
          isImporting ? "opacity-50 cursor-not-allowed" : "hover:bg-orange-700 cursor-pointer"
        }`}>
          <span>{isImporting ? "Importing..." : "Import List"}</span>
          <input
            type="file"
            disabled={isImporting}
            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
            className="hidden"
            onChange={handleImportChange}
          />
        </label>

        <button onClick={openAddModal} className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800">
          Add Medical Device CPR
        </button>
      </div>

      {/* Table Section */}
      {loading ? (
        <p className="text-gray-600">Loading...</p>
      ) : (
        <div className="overflow-auto max-h-[600px] bg-white rounded-xl shadow border">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50 sticky top-0 z-10">
              <tr>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Registration Number</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Product Name</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Manufacturer</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Country of Origin</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Trader</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Distributor</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Issuance Date</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Expiry Date</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Status</th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
              </tr>
            </thead>

            <tbody className="divide-y divide-gray-200">
              {records.length > 0 ? (
                records.map((row) => (
                  <tr key={row.registration_number}>
                    <td className="px-4 py-2 font-medium">{row.registration_number}</td>
                    <td className="px-4 py-2">{row.product_name}</td>
                    <td className="px-4 py-2">{row.manufacturer}</td>
                    <td className="px-4 py-2">{row.country_of_origin}</td>
                    <td className="px-4 py-2">{row.trader}</td>
                    <td className="px-4 py-2">{row.distributor}</td>
                    <td className="px-4 py-2">{row.issuance_date}</td>
                    <td className="px-4 py-2">{row.expiry_date}</td>
                    <td className="px-4 py-2">
                      {row.is_canceled === "Y" ? (
                        <span className="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Cancelled</span>
                      ) : (
                        <span className="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Active</span>
                      )}
                    </td>
                    <td className="whitespace-nowrap px-4 py-2">
                      <button
                        onClick={() => openEditModal(row)}
                        className="bg-green-600 text-white p-2 rounded hover:bg-green-900 inline-flex items-center justify-center transition-colors"
                        title="Edit CPR Details"
                      >
                        <Edit size={16} />
                      </button>
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="10" className="text-center py-6 text-gray-500">
                    No record found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Pagination Logic Control */}
      {!loading && records.length > 0 && (
        <div className="flex justify-between items-center mt-4">
          <p className="text-sm text-gray-600">
            Page <span className="font-bold">{page}</span> of <span className="font-bold">{lastPage}</span> | Total
            Records: <span className="font-bold">{total}</span>
          </p>

          <div className="flex gap-2">
            <button
              disabled={page === 1}
              onClick={() => fetchRecords(page - 1, search)}
              className={`px-4 py-2 rounded font-semibold ${
                page === 1 ? "bg-gray-300 text-gray-600 cursor-not-allowed" : "bg-[#286634] text-white hover:bg-[#1f4d27]"
              }`}
            >
              Prev
            </button>

            <button
              disabled={page === lastPage}
              onClick={() => fetchRecords(page + 1, search)}
              className={`px-4 py-2 rounded font-semibold ${
                page === lastPage ? "bg-gray-300 text-gray-600 cursor-not-allowed" : "bg-[#00bf63] text-white hover:bg-[#00994f]"
              }`}
            >
              Next
            </button>
          </div>
        </div>
      )}

      {/* Embedded Action Add/Edit Modal */}
      {modalOpen && (
        <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div className="bg-white p-6 rounded-xl shadow-lg w-96 max-h-[90vh] overflow-y-auto">
            <h2 className="text-lg font-bold mb-4">{editData ? "Edit Medical Device CPR" : "Add Medical Device CPR"}</h2>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Registration Number</label>
              <input
                type="text"
                placeholder="CMDR-YYYY-XXXX"
                disabled={!!editData}
                className={`border p-2 w-full rounded ${errors.registration_number ? "border-red-500" : ""} ${
                  editData ? "bg-gray-100 text-gray-500 cursor-not-allowed" : ""
                }`}
                value={form.registration_number}
                onChange={(e) => {
                  setForm({ ...form, registration_number: e.target.value });
                  if (errors.registration_number) setErrors({ ...errors, registration_number: null });
                }}
              />
              {errors.registration_number && <p className="text-red-500 text-xs mt-1">{errors.registration_number}</p>}
            </div>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Product Name</label>
              <input
                type="text"
                placeholder="Product Name"
                className={`border p-2 w-full rounded ${errors.product_name ? "border-red-500" : ""}`}
                value={form.product_name}
                onChange={(e) => {
                  setForm({ ...form, product_name: e.target.value });
                  if (errors.product_name) setErrors({ ...errors, product_name: null });
                }}
              />
              {errors.product_name && <p className="text-red-500 text-xs mt-1">{errors.product_name}</p>}
            </div>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Manufacturer</label>
              <input
                type="text"
                placeholder="Manufacturer"
                className="border p-2 w-full rounded"
                value={form.manufacturer}
                onChange={(e) => setForm({ ...form, manufacturer: e.target.value })}
              />
            </div>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Country of Origin</label>
              <input
                type="text"
                placeholder="Country of Origin"
                className="border p-2 w-full rounded"
                value={form.country_of_origin}
                onChange={(e) => setForm({ ...form, country_of_origin: e.target.value })}
              />
            </div>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Trader</label>
              <input
                type="text"
                placeholder="Trader"
                className="border p-2 w-full rounded"
                value={form.trader}
                onChange={(e) => setForm({ ...form, trader: e.target.value })}
              />
            </div>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Distributor</label>
              <input
                type="text"
                placeholder="Distributor"
                className={`border p-2 w-full rounded ${errors.distributor ? "border-red-500" : ""}`}
                value={form.distributor}
                onChange={(e) => {
                  setForm({ ...form, distributor: e.target.value });
                  if (errors.distributor) setErrors({ ...errors, distributor: null });
                }}
              />
              {errors.distributor && <p className="text-red-500 text-xs mt-1">{errors.distributor}</p>}
            </div>

            <div className="mb-2">
              <label className="text-xs font-semibold text-gray-600">Issuance Date</label>
              <input
                type="date"
                className="border p-2 w-full rounded"
                value={form.issuance_date}
                onChange={(e) => setForm({ ...form, issuance_date: e.target.value })}
              />
            </div>

            <div className="mb-4">
              <label className="text-xs font-semibold text-gray-600">Expiry Date</label>
              <input
                type="date"
                className="border p-2 w-full rounded"
                value={form.expiry_date}
                onChange={(e) => setForm({ ...form, expiry_date: e.target.value })}
              />
            </div>

            <div className="mb-4">
              <label className="text-xs font-semibold text-gray-600 block mb-1">Status</label>
              <select
                className="border p-2 w-full rounded"
                value={form.is_canceled}
                onChange={(e) => setForm({ ...form, is_canceled: e.target.value })}
              >
                <option value="N">Active</option>
                <option value="Y">Cancelled</option>
              </select>
            </div>

            <div className="flex justify-end space-x-2">
              <button
                onClick={() => {
                  if (window.confirm("Do you wish to cancel editing?")) {
                    setModalOpen(false);
                    setErrors({});
                  }
                }}
                className="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
              >
                Cancel
              </button>

              <button onClick={handleSubmit} className="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">
                {editData ? "Update" : "Add"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}