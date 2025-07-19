<?php
// Set the current tab (default is 'personal')
$currentTab = $_GET["tab"] ?? "personal";

// Function to determine active tab
function isActive($tab, $currentTab)
{
    return $tab === $currentTab ? "active" : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Profile</title>

  <!-- Global Styles (Optional) -->
  <link rel="stylesheet" href="index.css" />

  <!-- Component CSS -->
  <link rel="stylesheet" href="components/navbar/navbar.css" />
  <link rel="stylesheet" href="components/profile-card/profile-card.css" />
  <link rel="stylesheet" href="components/profile-info/profile-info.css" />
  <link rel="stylesheet" href="components/preferences/preferences.css" />
  <link rel="stylesheet" href="components/favorites/favorites.css" />
  <link rel="stylesheet" href="components/history/history.css" />
</head>
<body>

  <?php include "components/navbar/navbar.php"; ?>

  <div class="profile-container">

    <?php include "components/profile-card/profile-card.php"; ?>

    <!-- Tab Navigation -->
    <div class="tabs">
      <a href="?tab=personal" class="tab <?php echo isActive(
          "personal",
          $currentTab,
      ); ?>">Personal Info</a>
      <a href="?tab=preferences" class="tab <?php echo isActive(
          "preferences",
          $currentTab,
      ); ?>">Preferences</a>
      <a href="?tab=favorites" class="tab <?php echo isActive(
          "favorites",
          $currentTab,
      ); ?>">Favorites</a>
      <a href="?tab=history" class="tab <?php echo isActive(
          "history",
          $currentTab,
      ); ?>">History</a>
    </div>

    <!-- Tab Content -->
      <?php switch ($currentTab) {
          case "preferences":
              include "components/preferences/preferences.php";
              break;
          case "favorites":
              include "components/favorites/favorites.php";
              break;
          case "history":
              include "components/history/history.php";
              break;
          case "personal":
          default:
              include "components/profile-info/profile-info.php";
              break;
      } ?>

  </div>
</body>
</html>
