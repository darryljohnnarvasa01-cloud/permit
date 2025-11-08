<?php
require_once __DIR__ . '/../../app/core/session.php';
require_once __DIR__ . '/../../app/helpers/helper.php';
require_once __DIR__ . '/../../app/models/PermitType.php';
require_once __DIR__ . '/../../app/models/Log.php';

Session::start();
Session::requireRole('admin');

$permitTypeModel = new PermitType();
$logModel = new Log();

// Handle actions (create, update, delete, toggle)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'create') {
        $data = [
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description']),
            'requirements' => sanitize($_POST['requirements']),
            'fee' => floatval($_POST['fee']),
            'validity_months' => intval($_POST['validity_months']),
            'status' => 'active'
        ];
        
        if ($permitTypeModel->create($data)) {
            $logModel->add(Session::getUserId(), 'Permit Type Created', "Created: {$data['name']}");
            success('Permit type created successfully');
        }
    } elseif ($_POST['action'] === 'update') {
        $data = [
            'name' => sanitize($_POST['name']),
            'description' => sanitize($_POST['description']),
            'requirements' => sanitize($_POST['requirements']),
            'fee' => floatval($_POST['fee']),
            'validity_months' => intval($_POST['validity_months'])
        ];
        
        if ($permitTypeModel->update($_POST['id'], $data)) {
            $logModel->add(Session::getUserId(), 'Permit Type Updated', "Updated ID: {$_POST['id']}");
            success('Permit type updated successfully');
        }
    }
    redirect('/permit/public/admin/permit_types.php');
}

if (isset($_GET['delete'])) {
    if ($permitTypeModel->delete($_GET['delete'])) {
        $logModel->add(Session::getUserId(), 'Permit Type Deleted', "Deleted ID: {$_GET['delete']}");
        success('Permit type deleted successfully');
    }
    redirect('/permit/public/admin/permit_types.php');
}

if (isset($_GET['toggle'])) {
    if ($permitTypeModel->toggleStatus($_GET['toggle'])) {
        success('Status updated successfully');
    }
    redirect('/permit/public/admin/permit_types.php');
}

$permitTypes = $permitTypeModel->all();

$title = 'Permit Type Management';
require_once __DIR__ . '/../../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Colorful Header Section -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 rounded-2xl p-8 shadow-2xl mb-8 animate-scale-in">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Permit Type Management</h1>
                        <p class="mt-1 text-teal-100">Manage different types of motorela permits</p>
                    </div>
                </div>
            </div>
            <button onclick="openModal('createModal')" class="mt-4 sm:mt-0 bg-white text-teal-600 hover:bg-teal-50 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Permit Type
            </button>
        </div>
    </div>

    <div class="mt-8 card">
        <div class="overflow-x-auto">
            <table class="table">
                <thead class="table-header">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Fee</th>
                        <th class="px-4 py-3 text-left">Validity</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($permitTypes as $type): ?>
                    <tr class="table-row">
                        <td class="px-4 py-3">
                            <div class="font-medium"><?= escape($type['name']) ?></div>
                            <div class="text-sm text-gray-500"><?= escape($type['description']) ?></div>
                        </td>
                        <td class="px-4 py-3 font-medium"><?= formatMoney($type['fee']) ?></td>
                        <td class="px-4 py-3"><?= $type['validity_months'] ?> months</td>
                        <td class="px-4 py-3"><?= getStatusBadge($type['status']) ?></td>
                        <td class="px-4 py-3 space-x-2">
                            <button onclick='editPermitType(<?= json_encode($type) ?>)' class="text-primary-600 hover:text-primary-900">Edit</button>
                            <a href="?toggle=<?= $type['id'] ?>" class="text-yellow-600 hover:text-yellow-900">Toggle</a>
                            <a href="?delete=<?= $type['id'] ?>" onclick="return confirm('Delete?')" class="text-red-600 hover:text-red-900">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl mx-4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Add New Permit Type</h3>
            <button onclick="closeModal('createModal')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Name</label>
                    <input type="text" name="name" class="input" required>
                </div>
                <div>
                    <label class="label">Fee (₱)</label>
                    <input type="number" name="fee" step="0.01" class="input" required>
                </div>
                <div>
                    <label class="label">Validity (months)</label>
                    <input type="number" name="validity_months" class="input" required value="12">
                </div>
                <div class="col-span-2">
                    <label class="label">Description</label>
                    <textarea name="description" class="input" rows="2"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="label">Requirements (comma-separated)</label>
                    <textarea name="requirements" class="input" rows="3" placeholder="Valid ID,Driver's License,OR/CR"></textarea>
                </div>
                <div class="col-span-2 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl mx-4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Edit Permit Type</h3>
            <button onclick="closeModal('editModal')" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="label">Name</label>
                    <input type="text" name="name" id="edit_name" class="input" required>
                </div>
                <div>
                    <label class="label">Fee (₱)</label>
                    <input type="number" name="fee" id="edit_fee" step="0.01" class="input" required>
                </div>
                <div>
                    <label class="label">Validity (months)</label>
                    <input type="number" name="validity_months" id="edit_validity_months" class="input" required>
                </div>
                <div class="col-span-2">
                    <label class="label">Description</label>
                    <textarea name="description" id="edit_description" class="input" rows="2"></textarea>
                </div>
                <div class="col-span-2">
                    <label class="label">Requirements (comma-separated)</label>
                    <textarea name="requirements" id="edit_requirements" class="input" rows="3"></textarea>
                </div>
                <div class="col-span-2 flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editPermitType(type) {
    document.getElementById('edit_id').value = type.id;
    document.getElementById('edit_name').value = type.name;
    document.getElementById('edit_description').value = type.description;
    document.getElementById('edit_requirements').value = type.requirements;
    document.getElementById('edit_fee').value = type.fee;
    document.getElementById('edit_validity_months').value = type.validity_months;
    openModal('editModal');
}
</script>

<?php require_once __DIR__ . '/../../resources/views/layouts/footer.php'; ?>
