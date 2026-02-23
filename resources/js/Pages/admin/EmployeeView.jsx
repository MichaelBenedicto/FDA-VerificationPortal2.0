import React, { useEffect, useState } from "react";
import axios from "axios";
import { useSearchParams } from "react-router-dom";
import { 
  User, 
  Briefcase, 
  Building2, 
  CircleCheck, 
  CircleX,
  Shield
} from "lucide-react";

export default function EmployeeView() {
  const [searchParams] = useSearchParams();
  const idNumber = searchParams.get("ID_NUMBER");
  
  const [employee, setEmployee] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!idNumber) {
      setError("No employee ID provided");
      setLoading(false);
      return;
    }

    fetchEmployeeDetails();
  }, [idNumber]);

  const fetchEmployeeDetails = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`/admin/hr/view/${idNumber}`);
      setEmployee(response.data);
      setError(null);
    } catch (err) {
      console.error("Error fetching employee:", err);
      setError("Employee not found or error loading data");
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-gray-50 via-green-50 to-emerald-50 flex items-center justify-center">
        <div className="text-center">
          <div className="inline-block animate-spin rounded-full h-16 w-16 border-b-4 border-[#00bf63] mb-4"></div>
          <p className="text-xl font-semibold text-[#286634]">Loading employee details...</p>
        </div>
      </div>
    );
  }

  if (error || !employee) {
    return (
      <div className="min-h-screen bg-gradient-to-br from-gray-50 via-red-50 to-rose-50 flex items-center justify-center p-4">
        <div className="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full text-center">
          <div className="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <CircleX size={40} className="text-red-600" />
          </div>
          <h2 className="text-2xl font-bold text-gray-900 mb-2">Employee Not Found</h2>
          <p className="text-gray-600 mb-6">{error}</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50 to-teal-50">
      {/* Header */}
      <div className="bg-gradient-to-r from-[#286634] to-[#00bf63] text-white shadow-xl print:hidden">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-2xl sm:text-3xl font-bold tracking-tight">Employee Details</h1>
              <p className="text-green-100 text-sm mt-1">Food and Drug Administration</p>
            </div>
          </div>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Verification Badge */}
        <div className="bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-2xl p-6 mb-8 shadow-lg print:bg-white print:border-2 print:border-emerald-600 print:text-emerald-900">
          <div className="flex items-center gap-3">
            <div className="bg-white/20 p-3 rounded-full">
              <Shield size={32} className="text-white" />
            </div>
            <div>
              <h2 className="text-xl font-bold">Verified FDA Employee</h2>
              <p className="text-emerald-100 text-sm">Official employee verification record</p>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column - Employee Photo & Quick Info */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-3xl shadow-xl overflow-hidden sticky top-8">
              {/* Photo */}
              <div className="bg-gradient-to-br from-[#286634] to-[#00bf63] p-8">
                <div className="bg-white rounded-2xl p-1 shadow-2xl">
                  {employee.EMP_PICTURE ? (
                    <img
                      src={employee.EMP_PICTURE}
                      alt={employee.EMP_NAME}
                      className="w-full aspect-square object-cover rounded-xl"
                    />
                  ) : (
                    <div className="w-full aspect-square bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center">
                      <User size={80} className="text-gray-400" />
                    </div>
                  )}
                </div>
              </div>

              {/* Status Badge */}
              <div className="px-6 -mt-6 relative z-10">
                <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-full font-bold text-sm shadow-lg ${
                  employee.STATUS === 'Y' 
                    ? 'bg-green-500 text-white' 
                    : 'bg-red-500 text-white'
                }`}>
                  {employee.STATUS === 'Y' ? (
                    <>
                      <CircleCheck size={18} />
                      Active Employee
                    </>
                  ) : (
                    <>
                      <CircleX size={18} />
                      Inactive
                    </>
                  )}
                </div>
              </div>

              {/* ID Number */}
              <div className="p-6 pt-4">
                <div className="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-4 border-2 border-gray-200">
                  <p className="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                    Employee ID
                  </p>
                  <p className="text-2xl font-bold text-[#286634] tracking-tight">
                    {employee.ID_NUMBER}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* Right Column - Detailed Information */}
          <div className="lg:col-span-2 space-y-6">
            {/* Personal Information Card */}
            <div className="bg-white rounded-3xl shadow-xl p-8">
              <div className="flex items-center gap-3 mb-6">
                <div className="bg-gradient-to-br from-[#00bf63] to-[#79af60] p-3 rounded-xl">
                  <User size={24} className="text-white" />
                </div>
                <h2 className="text-2xl font-bold text-gray-900">Personal Information</h2>
              </div>

              <div className="space-y-4">
                <div className="border-b border-gray-100 pb-4">
                  <label className="text-sm font-semibold text-gray-500 uppercase tracking-wide">
                    Full Name
                  </label>
                  <p className="text-xl font-bold text-gray-900 mt-1">
                    {employee.EMP_NAME}
                  </p>
                </div>

                <div className="border-b border-gray-100 pb-4">
                  <label className="text-sm font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <Briefcase size={16} />
                    Position / Designation
                  </label>
                  <p className="text-lg font-semibold text-gray-700 mt-1">
                    {employee.EMP_DESIGNATION}
                  </p>
                </div>

                <div className="border-b border-gray-100 pb-4">
                  <label className="text-sm font-semibold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <Building2 size={16} />
                    Office / Center
                  </label>
                  <p className="text-lg font-semibold text-gray-700 mt-1">
                    {employee.OFFICE_CENTER}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <style>{`
        @media print {
          body {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
          }
          
          @page {
            margin: 1.5cm;
          }
        }
      `}</style>
    </div>
  );
}
