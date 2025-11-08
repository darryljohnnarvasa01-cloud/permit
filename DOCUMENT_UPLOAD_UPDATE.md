# Document Upload System Update

## ✅ What Changed

The permit application form now has **individual, labeled upload fields** for each required document instead of a single multi-file upload field.

---

## 📋 New Document Upload Structure

### Required Documents (Red Icon - Must Upload)
1. **Valid ID** 
   - UMID, Driver's License, Passport, Voter's ID, SSS, PhilHealth, etc.
   - Required field

2. **Driver's License**
   - Valid driver's license with restriction code 1, 2, or 3
   - Required field

3. **Vehicle OR (Official Receipt)**
   - Current year's Official Receipt from LTO
   - Required field

4. **Vehicle CR (Certificate of Registration)**
   - Certificate of Registration from LTO
   - Required field

### Recommended Documents (Blue Icon - Highly Recommended)
5. **Barangay Clearance**
   - Barangay clearance from your area
   - Optional but recommended

6. **Police Clearance**
   - National Police Clearance
   - Optional but recommended

### Optional Documents (Gray Icon - If Available)
7. **Cedula (Community Tax Certificate)**
   - Current year's Community Tax Certificate
   - Optional

8. **Proof of Ownership**
   - Deed of sale or other proof of vehicle ownership
   - Optional

9. **Insurance Certificate**
   - Vehicle insurance certificate if available
   - Optional

10. **Other Supporting Documents**
    - Any other relevant documents
    - Optional

---

## 🎨 Visual Features

### Color-Coded Requirements
- 🔴 **Red Icon** = Required documents (must upload)
- 🔵 **Blue Icon** = Recommended documents
- ⚪ **Gray Icon** = Optional documents

### Each Upload Field Shows:
- Clear document label
- Requirement status (Required/Recommended/Optional)
- Description of what's needed
- File format accepted (images and PDF)

### Upload Guidelines Box
A helpful information box displays:
- Accepted formats: JPG, PNG, GIF, PDF
- Clear and readable requirements
- File size limit (5MB per document)
- Visibility requirements

---

## 📁 How It Works Now

### Before (Old System)
- One upload field
- Multiple files at once
- Generic "Document 1", "Document 2", etc.
- Hard to know what was uploaded

### After (New System)
- Separate field for each document type
- Clear labels for each requirement
- Easy to see what's uploaded
- Shows actual document names like "Valid ID", "Driver's License", etc.

---

## 💾 Data Storage

Documents are saved with their labels:
```json
{
  "Valid ID": "requirements/abc123_id.jpg",
  "Driver's License": "requirements/def456_license.pdf",
  "Vehicle OR": "requirements/ghi789_or.jpg",
  "Vehicle CR": "requirements/jkl012_cr.jpg"
}
```

---

## 🔄 Backward Compatibility

The system supports both:
- **New applications**: Shows document names (e.g., "Valid ID")
- **Old applications**: Shows generic names (e.g., "Document 1")

No data loss from previous uploads!

---

## 📱 User Experience Improvements

### For Applicants:
1. ✅ Clear understanding of what documents are needed
2. ✅ Easy to see which documents are required vs optional
3. ✅ Upload one document at a time with proper labeling
4. ✅ Helpful descriptions for each document
5. ✅ Visual indicators (icons and colors)
6. ✅ Upload guidelines clearly displayed

### For Admin/Staff:
1. ✅ See exactly what document was uploaded
2. ✅ Document names instead of generic numbers
3. ✅ Easier to verify if all requirements are complete
4. ✅ Better organization of documents

---

## 🎯 Benefits

### Clarity
- Applicants know exactly what to upload
- No confusion about document types
- Clear requirement levels (required/recommended/optional)

### Organization
- Each document properly labeled
- Easy to identify missing documents
- Better document tracking

### User-Friendly
- Professional card-based layout
- Hover effects for better UX
- Mobile responsive design
- Color-coded importance levels

---

## 📝 Form Validation

### Required Fields:
- Valid ID ✓
- Driver's License ✓
- Vehicle OR ✓
- Vehicle CR ✓

**The form won't submit without these 4 required documents.**

### Optional Fields:
All other documents can be skipped if not available.

---

## 🔧 Technical Details

### Files Modified:
1. **application_new.php**
   - Updated file upload handler (lines 17-42)
   - Replaced multi-file upload with individual labeled fields (lines 243-426)
   - Added document type definitions with keys and labels

2. **permit_view.php**
   - Updated document display to show proper labels (lines 234-283)
   - Maintains backward compatibility with old format
   - Shows document names instead of generic numbers

### Upload Process:
```php
// Each document has a unique field name
valid_id → "Valid ID"
drivers_license → "Driver's License"
vehicle_or → "Vehicle OR (Official Receipt)"
vehicle_cr → "Vehicle CR (Certificate of Registration)"
// etc...
```

---

## 🚀 How to Use

### For Applicants:
1. Go to "Apply for New Permit"
2. Fill out all vehicle and owner information
3. Scroll to "Upload Requirements" section
4. Upload each required document (marked with red *)
5. Optionally upload recommended/optional documents
6. Click "Submit Application"

### Testing:
1. Try uploading to required fields ✓
2. Try submitting without required docs (should show error) ✓
3. Try uploading optional docs ✓
4. Check if document names appear correctly in permit view ✓

---

## 📊 Migration Notes

- **No database changes required** ✓
- **Existing applications work normally** ✓
- **New applications use labeled uploads** ✓
- **Backward compatible with old data** ✓

---

## ✨ Summary

The new system provides a much better user experience with:
- 10 separate, labeled upload fields
- Clear requirement indicators
- Better organization
- Professional design
- Mobile friendly
- Backward compatible

**Status**: ✅ Complete and Ready to Use

**Implementation Date**: November 9, 2025
