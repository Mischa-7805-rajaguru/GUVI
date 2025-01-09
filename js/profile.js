$(document).ready(function (e) {
  // Retrieve user data from localStorage
  var user = JSON.parse(localStorage.getItem("user"));

  // Check if user data exists
  if (!user || !user[0] || !user[0].username) {
    // Redirect to login page if user data is missing
    window.location.href = "login.html";
    return;
  }

  var username = user[0]["username"];
  $("#username").text(username); // Display username on the profile page

  // Fetch user profile details (age, DOB, contact)
  $.ajax({
    url: "php/profile.php",
    type: "POST",
    data: { username: username, page: "profile_view" },
    success: function (response) {
      try {
        // Check if the response contains error
        if (response.error) {
          alert(response.error);
          return;
        }

        // Populate profile form with data
        if (response.length > 0) {
          var profile = response[0]; // Assuming the profile is returned as an array
          $("#txt_age").val(profile.age);
          $("#txt_dob").val(profile.dob);
          $("#txt_contact").val(profile.contact);
        } else {
          alert("No profile data available.");
        }
      } catch (error) {
        console.error("Error parsing response:", error);
        alert("An error occurred. Please try again later.");
      }
    },
    error: function () {
      alert("Error fetching profile details.");
    }
  });

  // Update Profile
  $(document).on("click", "#btn_profile_update", function (e) {
    e.preventDefault();

    var age = $("#txt_age").val();
    var dob = $("#txt_dob").val();
    var contact = $("#txt_contact").val();

    // Basic validation for empty fields
    if (!age || !dob || !contact) {
      $("#message").html(
        '<div class="alert alert-warning"><button type="button" class="close" data-dismiss="alert">&times;</button> All fields must be filled out!</div>'
      );
      return;
    }

    // Send the updated profile data to the server
    $.ajax({
      url: "php/profile.php",
      type: "POST",
      data: {
        username: username,
        page: "profile_update",
        age: age,
        dob: dob,
        contact: contact,
      },
      success: function (response) {
        if (response.status === 'Success') {
          $("#message").html(
            '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button> Profile updated successfully.</div>'
          );
          // Reload the page to reflect changes
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        } else {
          $("#message").html(
            '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button> Profile update failed. Please try again.</div>'
          );
        }
      },
      error: function () {
        $("#message").html(
          '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button> An error occurred. Please try again later.</div>'
        );
      },
    });
  });

  // Logout functionality
  $(document).on("click", "#btn_logout", function (e) {
    e.preventDefault();
    localStorage.removeItem("user"); // Remove user data from localStorage
    window.location.href = "login.html"; // Redirect to login page
  });
});
