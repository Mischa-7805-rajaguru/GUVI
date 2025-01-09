$(document).on("click", "#btn_signin", function (e) {
  e.preventDefault();

  var username = $("#txt_username").val().trim();
  var password = $("#txt_password").val().trim();

  if (username === "") {
    alert("Please enter username!");
  } else if (!/^[a-zA-Z]+$/.test(username)) {
    alert("Username can only contain letters (A-Z or a-z)!");
  } else if (password === "") {
    alert("Please enter password!");
  } else {
    $.ajax({
      url: "php/login.php",
      type: "post",
      data: { username: username, password: password },
      success: function (response) {
        console.log("Response from server:", response); // Debugging log

        if (response.trim() === "Success") {
          var a = [];
          a.push({ username: username, password: password });

          localStorage.setItem("user", JSON.stringify(a));

          console.log("Redirecting to profile.html"); // Debugging redirection
          window.location.href = "profile.html";
        } else {
          $("#message").html(
            '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button> Fail to Login </div>'
          );
        }
      },
      error: function () {
        alert("An error occurred. Please try again.");
      },
    });

    if ($("#signin_form").length > 0) {
      $("#signin_form")[0].reset();
    } else {
      console.error("Form with ID 'signin_form' not found in the DOM.");
    }
  }
});

$(document).on("click", "#btn_register_form", function () {
  window.location.href = "register.html";
});
