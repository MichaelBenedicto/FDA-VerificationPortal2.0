import React, { useEffect, useState } from "react";
import axios from "axios";
import validate, { hasErrors, formatErrors } from "./validate";
import { Edit, Eye } from "lucide-react";

export default function FdaHRTable() {
  const [employees, setEmployees] = useState([]);
  const [searchInput, setSearchInput] = useState("");
  const [search, setSearch] = useState("");
  const [loading, setLoading] = useState(true);

  // pagination states
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [total, setTotal] = useState(0);

  // For add/edit modal
  const [modalOpen, setModalOpen] = useState(false);
  const [editData, setEditData] = useState(null);
  const [form, setForm] = useState({
    ID_NUMBER: "",
    EMP_NAME: "",
    EMP_DESIGNATION: "",
    OFFICE_CENTER: "",
    STATUS: "Y",
    EMP_PICTURE: "",
  });

  // File upload state
  const [imageFile, setImageFile] = useState(null);
  const [imagePreview, setImagePreview] = useState(null);

  // Validation errors state
  const [errors, setErrors] = useState({});

  // Notification state
  const [notification, setNotification] = useState({
    show: false,
    type: '', // 'success' or 'error'
    message: ''
  });

  // Auto-hide notification after 5 seconds
  useEffect(() => {
    if (notification.show) {
      const timer = setTimeout(() => {
        setNotification({ show: false, type: '', message: '' });
      }, 5000);
      return () => clearTimeout(timer);
    }
  }, [notification.show]);

  const showNotification = (type, message) => {
    setNotification({ show: true, type, message });
  };

  const handleFileChange = (e) => {
    const file = e.target.files[0];
    
    if (file) {
      // Check file size (5MB = 5 * 1024 * 1024 bytes)
      const maxSize = 5 * 1024 * 1024;
      
      if (file.size > maxSize) {
        setErrors({ ...errors, EMP_PICTURE: 'File size must be less than 5MB' });
        e.target.value = ''; // Clear the input
        return;
      }

      // Check file type
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      if (!allowedTypes.includes(file.type)) {
        setErrors({ ...errors, EMP_PICTURE: 'Only JPG, JPEG, PNG, and GIF files are allowed' });
        e.target.value = ''; // Clear the input
        return;
      }

      // Clear any previous errors
      const newErrors = { ...errors };
      delete newErrors.EMP_PICTURE;
      setErrors(newErrors);

      // Set file and preview
      setImageFile(file);
      
      // Create preview URL
      const reader = new FileReader();
      reader.onloadend = () => {
        setImagePreview(reader.result);
      };
      reader.readAsDataURL(file);
    }
  };

  const fetchEmployees = async (pageNumber = 1, searchValue = search) => {
  setLoading(true);
  try {
    const res = await axios.get("/fda/hr/list", { // Changed from /admin/hr/list
      params: {
        search: searchValue,
        page: pageNumber,
      },
    });

      setEmployees(res.data.data);
      setPage(res.data.current_page);
      setLastPage(res.data.last_page);
      setTotal(res.data.total);
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchEmployees(1, search);
  }, [search]);

  const openAddModal = () => {
    setEditData(null);
    setForm({
      ID_NUMBER: "",
      EMP_NAME: "",
      EMP_DESIGNATION: "",
      OFFICE_CENTER: "",
      STATUS: "Y",
      EMP_PICTURE: "",
    });
    setImageFile(null);
    setImagePreview(null);
    setErrors({}); // Clear errors
    setModalOpen(true);
  };

  const openEditModal = (emp) => {
    setEditData(emp);
    setForm({
      ID_NUMBER: emp.ID_NUMBER,
      EMP_NAME: emp.EMP_NAME,
      EMP_DESIGNATION: emp.EMP_DESIGNATION,
      OFFICE_CENTER: emp.OFFICE_CENTER,
      STATUS: emp.STATUS,
      EMP_PICTURE: emp.EMP_PICTURE,
    });
    setImageFile(null);
    setImagePreview(emp.EMP_PICTURE || null); // Show existing image if available
    setErrors({}); // Clear errors
    setModalOpen(true);
  };

  const handleSubmit = async () => {
    // Validate form with duplicate check
    const validationErrors = validate(form, employees, !!editData);
    setErrors(validationErrors);

    // Check if there are validation errors
    if (hasErrors(validationErrors)) {
      return;
    }

    try {
      const confirmMsg = editData
        ? "Do you want to update this employee?"
        : "Do you want to add this employee?";

      if (!window.confirm(confirmMsg)) return;

      // Create FormData for file upload
      const formData = new FormData();
      formData.append('ID_NUMBER', form.ID_NUMBER);
      formData.append('EMP_NAME', form.EMP_NAME);
      formData.append('EMP_DESIGNATION', form.EMP_DESIGNATION);
      formData.append('OFFICE_CENTER', form.OFFICE_CENTER);
      formData.append('STATUS', form.STATUS);
      
      // Append image file if selected
      if (imageFile) {
        formData.append('EMP_PICTURE', imageFile);
      } else if (form.EMP_PICTURE) {
        // Keep existing image URL if no new file selected
        formData.append('EMP_PICTURE', form.EMP_PICTURE);
      }

      if (editData) {
        await axios.post(`/fda/hr/update/${editData.ID_NUMBER}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        showNotification('success', 'Employee updated successfully!');
      } else {
        await axios.post("/fda/hr/add", formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
          },
        });
        showNotification('success', 'Employee added successfully!');
      }

      // Close modal after a short delay to show notification
      setTimeout(() => {
        setModalOpen(false);
        setErrors({});
        setImageFile(null);
        setImagePreview(null);
      }, 500);
      
      fetchEmployees();
    } catch (err) {
      console.error(err.response?.data || err.message);

      // Format Laravel validation errors
      if (err.response?.data?.errors) {
        const formatFieldName = (fieldName) => {
          return fieldName
            .replace(/_/g, ' ')
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
        };

        const errorList = Object.entries(err.response.data.errors)
          .map(([field, messages]) => {
            const formattedField = formatFieldName(field);
            return messages.map(msg => {
              return msg.replace(/The\s+([a-z\s]+)\s+field/i, `The ${formattedField} field`);
            }).join('\n');
          })
          .join('\n');

        showNotification('error', errorList);
      } else {
        showNotification('error', err.response?.data?.message || err.message || "Something went wrong. Please try again.");
      }
    }
  };

  const handleSearch = () => {
    setSearch(searchInput);
  };

  const handleFullList = () => {
    setSearchInput("");
    setSearch("");
  };

  return (
    <div>
      {/* Notification */}
      {notification.show && (
        <div
          className={`fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-md ${
            notification.type === 'success'
              ? 'bg-green-100 border border-green-400 text-green-800'
              : 'bg-red-100 border border-red-400 text-red-800'
          }`}
          style={{
            animation: 'slideInRight 0.3s ease-out'
          }}
        >
          <div className="flex items-start">
            <div className="flex-shrink-0">
              {notification.type === 'success' ? (
                <svg className="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                </svg>
              ) : (
                <svg className="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
                </svg>
              )}
            </div>
            <div className="ml-3 flex-1">
              <p className="text-sm font-medium whitespace-pre-line">{notification.message}</p>
            </div>
            <button
              onClick={() => setNotification({ show: false, type: '', message: '' })}
              className="ml-4 flex-shrink-0"
            >
              <svg className="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clipRule="evenodd" />
              </svg>
            </button>
          </div>
        </div>
      )}

      <style>{`
        @keyframes slideInRight {
          from {
            transform: translateX(100%);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
      `}</style>

      {/* Search Bar */}
      <div className="flex flex-wrap items-center mb-4 gap-2">
        <input
          type="text"
          placeholder="Search employee..."
          className="border p-2 rounded w-full md:w-1/3"
          value={searchInput}
          onChange={(e) => setSearchInput(e.target.value)}
        />

        <button
          onClick={handleSearch}
          className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
          Search
        </button>
        <button
          onClick={handleFullList}
          className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800"
        >
          Full List
        </button>

        <button
          onClick={() => window.open("/fda/hr/download", "_blank")}
          className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800"
        >
          Download List
        </button>

        <button
          onClick={openAddModal}
          className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-800"
        >
          Add Employee
        </button>
      </div>

      {/* Table */}
      {loading ? (
        <p className="text-gray-600">Loading...</p>
      ) : (
        <div className="overflow-auto max-h-[600px] bg-white rounded-xl shadow border">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50 sticky top-0 z-10">
              <tr>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">
                  ID
                </th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">
                  Name
                </th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">
                  Designation
                </th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">
                  Office
                </th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">
                  Status
                </th>
                <th className="px-4 py-2 text-left text-sm font-medium text-gray-700">
                  Actions
                </th>
              </tr>
            </thead>

            <tbody className="divide-y divide-gray-200">
              {employees.length > 0 ? (
                employees.map((emp) => (
                  <tr key={emp.ID_NUMBER}>
                    <td className="px-4 py-2">{emp.ID_NUMBER}</td>
                    <td className="px-4 py-2">{emp.EMP_NAME}</td>
                    <td className="px-4 py-2">{emp.EMP_DESIGNATION}</td>
                    <td className="px-4 py-2">{emp.OFFICE_CENTER}</td>
                    <td className="px-4 py-2">
                      {emp.STATUS === "Y" ? (
                        <span className="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">
                          Active
                        </span>
                      ) : (
                        <span className="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">
                          Inactive
                        </span>
                      )}
                    </td>

                    <td className="space-x-3">
                      <button
                        onClick={() => openEditModal(emp)}
                        className="bg-green-600 text-white p-2 rounded hover:bg-green-900 inline-flex items-center justify-center transition-colors"
                        title="Edit Employee Details"
                      >
                        <Edit size={16} />
                      </button>

                      <a
  href={`http://127.0.0.1:8000/ADMIN_FDA_EMPLOYEESview.php?showdetail=&ID_NUMBER=${emp.ID_NUMBER}`}
  target="_blank"
  className="bg-blue-600 text-white p-2 rounded hover:bg-blue-900 inline-flex items-center justify-center transition-colors"
  rel="noreferrer"
  title="View Employee"
>
  <Eye size={16} />
</a>

                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="6" className="text-center py-6 text-gray-500">
                    No record found.
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      {/* Pagination */}
      {!loading && employees.length > 0 && (
        <div className="flex justify-between items-center mt-4">
          <p className="text-sm text-gray-600">
            Page <span className="font-bold">{page}</span> of{" "}
            <span className="font-bold">{lastPage}</span> | Total Records:{" "}
            <span className="font-bold">{total}</span>
          </p>

          <div className="flex gap-2">
            <button
              disabled={page === 1}
              onClick={() => fetchEmployees(page - 1, search)}
              className={`px-4 py-2 rounded font-semibold ${
                page === 1
                  ? "bg-gray-300 text-gray-600 cursor-not-allowed"
                  : "bg-[#286634] text-white hover:bg-[#1f4d27]"
              }`}
            >
              Prev
            </button>

            <button
              disabled={page === lastPage}
              onClick={() => fetchEmployees(page + 1, search)}
              className={`px-4 py-2 rounded font-semibold ${
                page === lastPage
                  ? "bg-gray-300 text-gray-600 cursor-not-allowed"
                  : "bg-[#00bf63] text-white hover:bg-[#00994f]"
              }`}
            >
              Next
            </button>
          </div>
        </div>
      )}

      {/* Modal */}
      {modalOpen && (
        <div className="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
          <div className="bg-white p-6 rounded-xl shadow-lg w-96 max-h-[90vh] overflow-y-auto">
            <h2 className="text-lg font-bold mb-4">
              {editData ? "Edit Employee" : "Add Employee"}
            </h2>

            {!editData && (
              <div className="mb-2">
                <input
                  type="text"
                  placeholder="ID Number"
                  className={`border p-2 w-full rounded ${
                    errors.ID_NUMBER ? 'border-red-500' : ''
                  }`}
                  required
                  value={form.ID_NUMBER}
                  onChange={(e) => {
                    setForm({ ...form, ID_NUMBER: e.target.value });
                    if (errors.ID_NUMBER) {
                      setErrors({ ...errors, ID_NUMBER: null });
                    }
                  }}
                />
                {errors.ID_NUMBER && (
                  <p className="text-red-500 text-xs mt-1">{errors.ID_NUMBER}</p>
                )}
              </div>
            )}

            <div className="mb-2">
              <input
                type="text"
                placeholder="Employee Name"
                className={`border p-2 w-full rounded ${
                  errors.EMP_NAME ? 'border-red-500' : ''
                }`}
                value={form.EMP_NAME}
                onChange={(e) => {
                  setForm({ ...form, EMP_NAME: e.target.value });
                  if (errors.EMP_NAME) {
                    setErrors({ ...errors, EMP_NAME: null });
                  }
                }}
              />
              {errors.EMP_NAME && (
                <p className="text-red-500 text-xs mt-1">{errors.EMP_NAME}</p>
              )}
            </div>

            <div className="mb-2">
              <input
                type="text"
                name="EMP_DESIGNATION"
                placeholder="Designation"
                className={`border p-2 w-full rounded ${
                  errors.EMP_DESIGNATION ? 'border-red-500' : ''
                }`}
                value={form.EMP_DESIGNATION}
                onChange={(e) => {
                  setForm({ ...form, EMP_DESIGNATION: e.target.value });
                  if (errors.EMP_DESIGNATION) {
                    setErrors({ ...errors, EMP_DESIGNATION: null });
                  }
                }}
              />
              {errors.EMP_DESIGNATION && (
                <p className="text-red-500 text-xs mt-1">{errors.EMP_DESIGNATION}</p>
              )}
            </div>

            <div className="mb-4">
              <select
                className={`border p-2 w-full rounded ${
                  errors.OFFICE_CENTER ? 'border-red-500' : ''
                }`}
                value={form.OFFICE_CENTER}
                onChange={(e) => {
                  setForm({ ...form, OFFICE_CENTER: e.target.value });
                  if (errors.OFFICE_CENTER) {
                    setErrors({ ...errors, OFFICE_CENTER: null });
                  }
                }}
              >
                <option value="">-- Select Office / Center --</option>

                <option value="Common Services Laboratory - Davao Testing Quality Assurance Laboratory">
                  Common Services Laboratory - Davao Testing Quality Assurance Laboratory
                </option>

                <option value="Field Regulatory Operations Office - Mindanao East Cluster">
                  Field Regulatory Operations Office - Mindanao East Cluster
                </option>

                <option value="Administration and Finance Office">
                  Administration and Finance Office
                </option>

                <option value="Common Services Laboratory - Alabang Testing Quality Assurance Laboratory">
                  Common Services Laboratory - Alabang Testing Quality Assurance Laboratory
                </option>

                <option value="Center for Drug Regulation and Research">
                  Center for Drug Regulation and Research
                </option>

                <option value="Field Regulatory Operations Office - North Luzon Cluster">
                  Field Regulatory Operations Office - North Luzon Cluster
                </option>

                <option value="Center for Device Regulation, Radiation Health, and Research">
                  Center for Device Regulation, Radiation Health, and Research
                </option>

                <option value="Information Communication Technology Management Division">
                  Information Communication Technology Management Division
                </option>

                <option value="Field Regulatory Operations Office - South Luzon Cluster">
                  Field Regulatory Operations Office - South Luzon Cluster
                </option>

                <option value="Center for Cosmetics and Household/Urban Hazardous Substances Regulation and Research">
                  Center for Cosmetics and Household/Urban Hazardous Substances Regulation and Research
                </option>

                <option value="Center for Food Regulation and Research">
                  Center for Food Regulation and Research
                </option>

                <option value="Field Regulatory Operations Office - Visayas Cluster">
                  Field Regulatory Operations Office - Visayas Cluster
                </option>

                <option value="Legal Services Support Center">
                  Legal Services Support Center
                </option>

                <option value="Office of the Deputy Director General - Administration and Finance Office">
                  Office of the Deputy Director General - Administration and Finance Office
                </option>

                <option value="Policy and Planning Office">
                  Policy and Planning Office
                </option>

                <option value="Common Services Laboratory - Cebu Testing Quality Assurance Laboratory">
                  Common Services Laboratory - Cebu Testing Quality Assurance Laboratory
                </option>

                <option value="Food and Drug Action Center">
                  Food and Drug Action Center
                </option>

                <option value="Field Regulatory Operations Office - Mindanao West Cluster">
                  Field Regulatory Operations Office - Mindanao West Cluster
                </option>

                <option value="Regulatory Enforcement Unit - Visayas Cluster">
                  Regulatory Enforcement Unit - Visayas Cluster
                </option>

                <option value="Regulatory Enforcement Unit - North Luzon Cluster">
                  Regulatory Enforcement Unit - North Luzon Cluster
                </option>

                <option value="Regulatory Enforcement Unit - Mindanao East Cluster">
                  Regulatory Enforcement Unit - Mindanao East Cluster
                </option>

                <option value="Regulatory Enforcement Unit - Mindanao West Cluster">
                  Regulatory Enforcement Unit - Mindanao West Cluster
                </option>

                <option value="Regulatory Enforcement Unit - South Luzon Cluster">
                  Regulatory Enforcement Unit - South Luzon Cluster
                </option>

                <option value="Office of the Deputy Director General - Field Regulatory Operations Office">
                  Office of the Deputy Director General - Field Regulatory Operations Office
                </option>

                <option value="Office of the Director General">
                  Office of the Director General
                </option>

                <option value="Office of the Director General - BAC">
                  Office of the Director General - BAC
                </option>

                <option value="Information Communication Technology Management Division - Records">
                  Information Communication Technology Management Division - Records
                </option>
              </select>
              {errors.OFFICE_CENTER && (
                <p className="text-red-500 text-xs mt-1">{errors.OFFICE_CENTER}</p>
              )}
            </div>

            {/* Image Upload */}
            <div className="mb-4">
              <label className="block text-sm font-medium text-gray-700 mb-2">
                Employee Picture
              </label>
              <input
                type="file"
                accept="image/jpeg,image/jpg,image/png,image/gif"
                onChange={handleFileChange}
                className={`border p-2 w-full rounded ${
                  errors.EMP_PICTURE ? 'border-red-500' : ''
                }`}
              />
              <p className="text-xs text-gray-500 mt-1">
                Allowed: JPG, JPEG, PNG, GIF. Max size: 5MB
              </p>
              {errors.EMP_PICTURE && (
                <p className="text-red-500 text-xs mt-1">{errors.EMP_PICTURE}</p>
              )}
              
              {/* Image Preview */}
              {imagePreview && (
                <div className="mt-3">
                  <p className="text-xs text-gray-600 mb-2">Preview:</p>
                  <div className="relative inline-block">
                    <img
                      src={imagePreview}
                      alt="Preview"
                      className="w-32 h-32 object-cover rounded border border-gray-300"
                    />
                    <button
                      type="button"
                      onClick={() => {
                        setImageFile(null);
                        setImagePreview(null);
                        setForm({ ...form, EMP_PICTURE: '' });
                      }}
                      className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600"
                      title="Remove image"
                    >
                      ×
                    </button>
                  </div>
                </div>
              )}
            </div>

            <select
              className="border p-2 w-full mb-4 rounded"
              value={form.STATUS}
              onChange={(e) => setForm({ ...form, STATUS: e.target.value })}
            >
              <option value="Y">Active</option>
              <option value="N">Inactive</option>
            </select>

            <div className="flex justify-end space-x-2">
              <button
                onClick={() => {
                  if (window.confirm("Do you wish to cancel?")) {
                    setModalOpen(false);
                    setErrors({});
                    setImageFile(null);
                    setImagePreview(null);
                  }
                }}
                className="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
              >
                Cancel
              </button>

              <button
                onClick={handleSubmit}
                className="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700"
              >
                {editData ? "Update" : "Add"}
              </button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
