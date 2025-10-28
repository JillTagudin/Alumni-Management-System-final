<!-- Staff Approval Modal Component -->
<div id="staffApprovalModal" class="staff-approval-modal" style="display: none;">
    <div class="staff-approval-modal-overlay"></div>
    <div class="staff-approval-modal-content">
        <div class="staff-approval-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" fill="#10B981"/>
                <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="staff-approval-message">
            <h3>Action Submitted for Approval</h3>
            <p>Your request has been submitted and is awaiting approval from a superior. You will be notified once it has been reviewed.</p>
        </div>
        <div class="staff-approval-actions">
            <button type="button" class="staff-approval-btn" onclick="closeStaffApprovalModal()">OK</button>
        </div>
    </div>
</div>

<style>
.staff-approval-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.staff-approval-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.staff-approval-modal-content {
    position: relative;
    background: white;
    border-radius: 12px;
    padding: 32px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
}

.staff-approval-icon {
    margin-bottom: 16px;
}

.staff-approval-message h3 {
    color: #10B981;
    font-size: 18px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.staff-approval-message p {
    color: #6B7280;
    font-size: 14px;
    line-height: 1.5;
    margin: 0 0 24px 0;
}

.staff-approval-actions {
    width: 100%;
}

.staff-approval-btn {
    background-color: #10B981;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.2s;
}

.staff-approval-btn:hover {
    background-color: #059669;
}

.staff-approval-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
}
</style>

<script>
function showStaffApprovalModal() {
    document.getElementById('staffApprovalModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeStaffApprovalModal() {
    document.getElementById('staffApprovalModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    // Redirect to pending changes page
    window.location.href = '/staff/pending-changes';
}

// Close modal when clicking overlay
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('staffApprovalModal');
    const overlay = modal?.querySelector('.staff-approval-modal-overlay');
    
    if (overlay) {
        overlay.addEventListener('click', closeStaffApprovalModal);
    }
});
</script>