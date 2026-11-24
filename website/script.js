// ==============================================================================
// CampusVoice: Interactive Client-Side Logic & AJAX Handlers
// ==============================================================================

document.addEventListener('DOMContentLoaded', () => {
  // 1. Grievance Form Submission
  const grievanceForm = document.getElementById('grievanceForm');
  const submitBtn = document.getElementById('submitBtn');
  const successModal = document.getElementById('successModal');
  const newTicketId = document.getElementById('newTicketId');
  const trackLink = document.getElementById('trackLink');
  const closeModalBtn = document.getElementById('closeModalBtn');
  const copyTicketBtn = document.getElementById('copyTicketBtn');

  if (grievanceForm) {
    grievanceForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span>Submitting Grievance...</span>';

      const formData = new FormData(grievanceForm);
      formData.append('action', 'submit');

      try {
        const response = await fetch('api.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          newTicketId.textContent = data.ticket_id;
          trackLink.href = `track.php?query=${data.ticket_id}`;
          successModal.style.display = 'flex';
          grievanceForm.reset();
        } else {
          alert('Submission Error: ' + (data.error || 'Failed to lodge grievance.'));
        }
      } catch (err) {
        alert('Network / Server Error: ' + err.message);
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<span>Submit Grievance</span><span class="btn-arrow">→</span>';
      }
    });
  }

  // 2. Close Modal Handlers
  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', () => {
      successModal.style.display = 'none';
      window.location.reload();
    });
  }

  // 3. Copy Ticket ID to Clipboard
  if (copyTicketBtn) {
    copyTicketBtn.addEventListener('click', () => {
      const ticket = newTicketId.textContent;
      navigator.clipboard.writeText(ticket).then(() => {
        copyTicketBtn.textContent = '✅';
        setTimeout(() => copyTicketBtn.textContent = '📋', 2000);
      });
    });
  }

  // 4. Admin Update Modal Handlers
  const adminModal = document.getElementById('adminModal');
  const adminUpdateForm = document.getElementById('adminUpdateForm');
  const closeAdminModal = document.getElementById('closeAdminModal');
  const cancelModalBtn = document.getElementById('cancelModalBtn');
  const modalTicketId = document.getElementById('modal_ticket_id');
  const modalTicketDisplay = document.getElementById('modal_ticket_display');
  const modalStatus = document.getElementById('modal_status');
  const modalRemarks = document.getElementById('modal_remarks');

  document.querySelectorAll('.open-update-modal').forEach(btn => {
    btn.addEventListener('click', () => {
      const ticket = btn.dataset.ticket;
      const student = btn.dataset.student;
      const status = btn.dataset.status;
      const remarks = btn.dataset.remarks;

      modalTicketId.value = ticket;
      modalTicketDisplay.innerHTML = `<strong>${ticket}</strong> (${student})`;
      modalStatus.value = status;
      modalRemarks.value = remarks;

      adminModal.style.display = 'flex';
    });
  });

  const hideAdminModal = () => {
    if (adminModal) adminModal.style.display = 'none';
  };

  if (closeAdminModal) closeAdminModal.addEventListener('click', hideAdminModal);
  if (cancelModalBtn) cancelModalBtn.addEventListener('click', hideAdminModal);

  // 5. Admin Form Submission
  if (adminUpdateForm) {
    adminUpdateForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const saveBtn = document.getElementById('saveActionBtn');
      saveBtn.disabled = true;
      saveBtn.textContent = 'Saving Changes...';

      const formData = new FormData(adminUpdateForm);
      formData.append('action', 'update_status');

      try {
        const response = await fetch('api.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();
        if (data.success) {
          hideAdminModal();
          window.location.reload();
        } else {
          alert('Error: ' + (data.error || 'Failed to update grievance status.'));
        }
      } catch (err) {
        alert('Network Error: ' + err.message);
      } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save & Broadcast Update';
      }
    });
  }
});
