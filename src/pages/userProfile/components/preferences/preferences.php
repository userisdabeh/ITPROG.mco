<div class="tab-content">
    <h2 class="section-header">Adoption Preferences</h2>
    <p class="section-subheader">Set your preferences to help us recommend the perfect pets for you</p>

  <form id="adoptionPreferencesForm">
    <div class="form-row">
      <div class="form-group">
        <label for="petType">Preferred Pet Type</label>
        <select id="petType">
          <option>Dog</option>
          <option>Cat</option>
          <option>Other</option>
        </select>
      </div>
      <div class="form-group">
        <label for="ageRange">Preferred Age Range</label>
        <select id="ageRange">
          <option>Any</option>
          <option>Puppy/Kitten</option>
          <option>Adult</option>
          <option>Senior</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="sizePreference">Preferred Size</label>
        <select id="sizePreference">
          <option>Any</option>
          <option>Small</option>
          <option>Medium</option>
          <option>Large</option>
        </select>
      </div>
      <div class="form-group">
        <label for="specialNeeds">Willing to adopt pets with special needs?</label>
        <select id="specialNeeds">
          <option>Yes</option>
          <option>No</option>
        </select>
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="save-btn">Save</button>
      <button type="button" class="cancel-btn">Cancel</button>
    </div>
  </form>
</div>

<div class="tab-content">
  <h2>Account Settings</h2>
  <form id="passwordForm">
    <div class="form-row">
      <div class="form-group">
        <label for="currentPassword">Current Password</label>
        <input type="password" id="currentPassword" placeholder="Enter current password">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="newPassword">New Password</label>
        <input type="password" id="newPassword" placeholder="Enter new password">
      </div>
      <div class="form-group">
        <label for="confirmPassword">Confirm New Password</label>
        <input type="password" id="confirmPassword" placeholder="Confirm new password">
      </div>
    </div>

    <div class="form-actions">
      <button type="button" class="save-btn">Update Password</button>
      <button type="button" class="cancel-btn">Cancel</button>
    </div>
  </form>
</div>
