# Payment Features Documentation

## New Features Added

### 1. Enhanced Document Viewing
- **Better Document Display**: Uploaded documents now show with icons based on file type (PDF, Image, etc.)
- **File Type Indicators**: Clear visual indicators for document types
- **Improved Document Names**: Shows document number and file format
- **One-Click Viewing**: Direct view button for each document

### 2. Payment Method Selection
Added support for multiple payment methods:
- Cash
- GCash
- PayMaya
- Bank Transfer
- Over the Counter

### 3. Payment Proof Upload
**For Applicants:**
- After permit approval, applicants can upload payment proof
- Upload button appears on the permit view page
- Supports images (JPG, PNG, GIF) and PDF files
- Must select payment method when uploading

**Upload Process:**
1. Navigate to "My Permits" or "Permit Details"
2. Click "Upload Payment Proof" button (appears when permit is approved)
3. Select payment method from dropdown
4. Upload clear photo or PDF of receipt
5. Submit

### 4. Payment Verification (Admin/Staff)
**For Admin/Staff:**
- View uploaded payment proofs directly on permit details page
- Image previews for photo receipts
- "Verify Payment" button to mark payment as paid
- Payment status automatically updates in the system

**Verification Process:**
1. Go to permit details page
2. View the uploaded payment proof in the Payment Information section
3. Click "Verify Payment" to confirm
4. Payment status changes to "Paid"

## Database Changes

### Migration File: `add_payment_method.sql`
Run this SQL file to add the payment method field:
```sql
USE motorela_permit_db;

ALTER TABLE motorela_permits 
ADD COLUMN IF NOT EXISTS payment_method ENUM('cash', 'gcash', 'paymaya', 'bank_transfer', 'over_counter') DEFAULT 'cash' AFTER payment_amount;
```

## Files Modified

1. **permit_view.php**
   - Enhanced document display with icons
   - Added payment information section
   - Payment proof upload modal
   - Payment verification for admin/staff

2. **motorela_manage.php (Admin)**
   - Added payment verification handler
   - Logs payment verification activities

3. **add_payment_method.sql**
   - Database migration for payment method field

## Usage Instructions

### For Applicants
1. Submit permit application
2. Wait for admin approval
3. Once approved, upload payment proof with method
4. Wait for payment verification
5. Once payment is verified, permit process is complete

### For Admin/Staff
1. Approve permit applications
2. Check permit details to view uploaded payment proofs
3. Verify payments after reviewing proof
4. System automatically logs all activities

## File Upload Locations
- **Documents/Requirements**: `/public/uploads/requirements/`
- **Payment Proofs**: `/public/uploads/payment_proofs/`

## Features Summary
✅ View uploaded documents with file type icons  
✅ Multiple payment method options  
✅ Easy payment proof upload for applicants  
✅ Image preview for payment receipts  
✅ Admin payment verification system  
✅ Activity logging for all payment actions  
✅ Mobile-responsive design  

## Security Notes
- Only permit owners or admin/staff can view permit details
- Payment proofs are stored securely in uploads directory
- All payment actions are logged for audit trail
- File uploads are validated and sanitized
