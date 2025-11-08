<?php
require_once __DIR__ . '/../../app/core/session.php';
require_once __DIR__ . '/../../app/helpers/helper.php';
require_once __DIR__ . '/../../app/models/Category.php';
require_once __DIR__ . '/../../app/models/Log.php';

Session::start();
Session::requireRole('admin');

$categoryModel = new Category();
$logModel = new Log();

// Handle Create
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $data = [
        'name' => sanitize($_POST['name']),
        'description' => sanitize($_POST['description']),
        'type' => $_POST['type'],
        'status' => 'active'
    ];
    
    $id = $categoryModel->create($data);
    if ($id) {
        $logModel->add(Session::getUserId(), 'Category Created', "Created category: {$data['name']}");
        success('Category created successfully');
    } else {
        error('Failed to create category');
    }
    redirect('/permit/public/admin/categories.php');
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = $_POST['id'];
    $data = [
        'name' => sanitize($_POST['name']),
        'description' => sanitize($_POST['description']),
        'type' => $_POST['type']
    ];
    
    if ($categoryModel->update($id, $data)) {
        $logModel->add(Session::getUserId(), 'Category Updated', "Updated category ID: $id");
        success('Category updated successfully');
    } else {
        error('Failed to update category');
    }
    redirect('/permit/public/admin/categories.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($categoryModel->delete($id)) {
        $logModel->add(Session::getUserId(), 'Category Deleted', "Deleted category ID: $id");
        success('Category deleted successfully');
    } else {
        error('Failed to delete category');
    }
    redirect('/permit/public/admin/categories.php');
}

// Handle Toggle Status
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    if ($categoryModel->toggleStatus($id)) {
        $logModel->add(Session::getUserId(), 'Category Status Changed', "Toggled status for category ID: $id");
        success('Status updated successfully');
    }
    redirect('/permit/public/admin/categories.php');
}

$groupedCategories = $categoryModel->getAllGroupedByType();

$title = 'Category Management';
require_once __DIR__ . '/../../resources/views/layouts/header.php';
?>

<div class="px-4 sm:px-6 lg:px-8 animate-fade-in">
    <!-- Colorful Header Section -->
    <div class="bg-gradient-to-r from-purple-600 via-blue-600 to-indigo-600 rounded-2xl p-8 shadow-2xl mb-8 animate-scale-in">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Category Management</h1>
                        <p class="mt-1 text-blue-100">Manage motorela categories (Vehicle Type, Zones, etc.)</p>
                    </div>
                </div>
            </div>
            <button onclick="openModal('createModal')" class="mt-4 sm:mt-0 bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Category
            </button>
        </div>
    </div>

    <!-- Categories by Type -->
    <div class="mt-8 space-y-6">
        <?php
        $typeLabels = [
            'vehicle_type' => 'Vehicle Types',
            'renewal_type' => 'Renewal Types',
            'fare_zone' => 'Fare Zones',
            'color_group' => 'Color Groups',
            'other' => 'Other Categories'
        ];
        
        foreach ($typeLabels as $type => $label):
            $categories = $groupedCategories[$type] ?? [];
        ?>
        <div class="card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4"><?= $label ?></h2>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead class="table-header">
                        <tr>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No categories in this type</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($categories as $cat): ?>
                        <tr class="table-row">
                            <td class="px-4 py-3 font-medium"><?= escape($cat['name']) ?></td>
                            <td class="px-4 py-3"><?= escape($cat['description']) ?></td>
                            <td class="px-4 py-3"><?= getStatusBadge($cat['status']) ?></td>
                            <td class="px-4 py-3 space-x-2">
                                <button onclick='editCategory(<?= json_encode($cat) ?>)' class="text-primary-600 hover:text-primary-900">Edit</button>
                                <a href="?toggle=<?= $cat['id'] ?>" class="text-yellow-600 hover:text-yellow-900">Toggle</a>
                                <a href="?delete=<?= $cat['id'] ?>" onclick="return confirm('Delete this category?')" class="text-red-600 hover:text-red-900">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md sm:max-w-lg mx-4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Add New Category</h3>
            <button onclick="closeModal('createModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="action" value="create">
            <div class="space-y-4">
                <div>
                    <label class="label">Type</label>
                    <select name="type" class="input" required>
                        <option value="vehicle_type">Vehicle Type</option>
                        <option value="renewal_type">Renewal Type</option>
                        <option value="fare_zone">Fare Zone</option>
                        <option value="color_group">Color Group</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="label">Name</label>
                    <input type="text" name="name" class="input" required>
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea name="description" class="input" rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('createModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md sm:max-w-lg mx-4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Edit Category</h3>
            <button onclick="closeModal('editModal')" class="text-gray-500 hover:text-gray-700">&times;</button>
        </div>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit_id">
            <div class="space-y-4">
                <div>
                    <label class="label">Type</label>
                    <select name="type" id="edit_type" class="input" required>
                        <option value="vehicle_type">Vehicle Type</option>
                        <option value="renewal_type">Renewal Type</option>
                        <option value="fare_zone">Fare Zone</option>
                        <option value="color_group">Color Group</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="label">Name</label>
                    <input type="text" name="name" id="edit_name" class="input" required>
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea name="description" id="edit_description" class="input" rows="3"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function editCategory(category) {
    document.getElementById('edit_id').value = category.id;
    document.getElementById('edit_name').value = category.name;
    document.getElementById('edit_description').value = category.description;
    document.getElementById('edit_type').value = category.type;
    openModal('editModal');
}
</script>

<?php require_once __DIR__ . '/../../resources/views/layouts/footer.php'; ?>
