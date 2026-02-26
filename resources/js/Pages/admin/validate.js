// validate.js - Validation functions for employee forms

const validate = (formData, existingEmployees = [], isEdit = false) => {
  const errors = {};

  // ID Number validation
  if (!formData.ID_NUMBER || formData.ID_NUMBER.trim() === '') {
    errors.ID_NUMBER = 'ID Number is required';
  } else if (!isEdit) {
    // Check for duplicate ID only when adding new employee
    const isDuplicate = existingEmployees.some(
      emp => emp.ID_NUMBER === formData.ID_NUMBER
    );
    if (isDuplicate) {
      errors.ID_NUMBER = 'This ID Number already exists';
    }
  }

  // Employee Name validation
  if (!formData.EMP_NAME || formData.EMP_NAME.trim() === '') {
    errors.EMP_NAME = 'Employee Name is required';
  } else if (formData.EMP_NAME.length < 2) {
    errors.EMP_NAME = 'Employee Name must be at least 2 characters';
  }

  // Designation validation
  if (!formData.EMP_DESIGNATION || formData.EMP_DESIGNATION.trim() === '') {
    errors.EMP_DESIGNATION = 'Employee Designation is required';
  }

  // Office/Center validation
  if (!formData.OFFICE_CENTER || formData.OFFICE_CENTER.trim() === '') {
    errors.OFFICE_CENTER = 'Office Center is required';
  }

  // Status validation
  if (!formData.STATUS) {
    errors.STATUS = 'Status is required';
  }

  return errors;
};

// Check if there are any errors
export const hasErrors = (errors) => {
  return Object.keys(errors).length > 0;
};

// Format error messages for display
export const formatErrors = (errors) => {
  return Object.values(errors).join('\n');
};

export default validate;