const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
  container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
  container.classList.remove("right-panel-active");
});

function handleGoogleLogin() {
  alert("Google login clicked! (You can integrate Google OAuth here)");
}

// 🚀 Login Validation
document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.getElementById("loginEmail").value.trim();
  const password = document.getElementById("loginPassword").value.trim();

  if (email === "" || password === "") {
    alert("Please fill in both email and password.");
  } else {
    // ✅ Simulate successful login
    window.location.href = "dashboard.html";
  }
});

// 🚀 Register Validation
document.getElementById("registerForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const name = document.getElementById("regName").value.trim();
  const email = document.getElementById("regEmail").value.trim();
  const password = document.getElementById("regPassword").value.trim();

  if (name === "" || email === "" || password === "") {
    alert("All fields are required for registration.");
  } else if (password.length < 6) {
    alert("Password must be at least 6 characters.");
  } else {
    alert("Registered successfully! You can now login.");
    container.classList.remove("right-panel-active"); // Go to login
  }
});
