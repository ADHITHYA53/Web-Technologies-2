document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("regForm");
    var ids = ["name", "roll", "email", "course", "pass", "cpass", "doc"];
    var errors = ["nameErr", "rollErr", "emailErr", "courseErr", "passErr", "cpassErr", "docErr"];
    var fields = [], errorFields = [];
    for (var i = 0; i < ids.length; i++) {
        fields.push(document.getElementById(ids[i]));
        errorFields.push(document.getElementById(errors[i]));
    }
    var msg = document.getElementById("statusMsg");
    var strength = document.getElementById("strengthText");
    var bar = document.getElementById("meterBar");
    var reset = document.getElementById("resetBtn");
    var submit = document.getElementById("submitBtn");
    function showError(i, text) {fields[i].classList.add("invalid");errorFields[i].textContent = text;return false;}
    function clearError(i) {fields[i].classList.remove("invalid");errorFields[i].textContent = "";return true;}
    function checkStrength() {
        var p = fields[4].value, score = 0;
        if (p.length >= 8) score++;
        if (/[A-Z]/.test(p)) score++;
        if (/[a-z]/.test(p)) score++;
        if (/[0-9]/.test(p)) score++;
        if (/[^A-Za-z0-9\s]/.test(p)) score++;
        var level = !p ? "None" : score === 5 ? "Strong" : score >= 3 ? "Medium" : "Weak";
        strength.textContent = "Strength: " + level;
        strength.className = level.toLowerCase();
        bar.className = "bar " + level.toLowerCase();
        return score === 5;
    }
    function validate(i) {
        var v = fields[i].value.trim();
        if (i === 0 && (!v || !/^[a-zA-Z\s.'-]{2,}$/.test(v)))
            return showError(i, "Enter a valid full name.");
        if (i === 1 && !/^[a-zA-Z0-9_-]{2,20}$/.test(v))
            return showError(i, "Enter a valid roll number (2-20 characters).");
        if (i === 2 && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v))
            return showError(i, "Enter a valid email address.");
        if (i === 3 && !v) return showError(i, "Please select a course.");
        if (i === 4 && (!v || !checkStrength()))
            return showError(i, "Use 8+ characters with uppercase, lowercase, number and special character.");
        if (i === 5 && (!v || v !== fields[4].value))
            return showError(i, "Passwords are required and must match.");
        if (i === 6) {
            var f = fields[6].files[0];
            if (!f) return showError(i, "Please upload a document.");
            if (!/\.(jpg|jpeg|png|pdf)$/i.test(f.name))
                return showError(i, "Only JPG, JPEG, PNG and PDF files are accepted.");
            if (f.size > 5 * 1024 * 1024)
                return showError(i, "File size must not exceed 5 MB.");
        }
        return clearError(i);
    }
    // Enter moves to the next field without submitting the form.
    for (let i = 0; i < fields.length; i++) {
        fields[i].addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                if (fields[i].value || (fields[i].type === "file" && fields[i].files.length))
                    validate(i);
                (fields[i + 1] || submit).focus();
            }
        });
        fields[i].addEventListener(fields[i].type === "file" || fields[i].tagName === "SELECT" ? "change" : "input", function () {
            if (i === 4) checkStrength();
            clearError(i);
            if (i === 4 && fields[5].value) validate(5);
        });
    }
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        var valid = true;
        for (var i = 0; i < fields.length; i++)
            if (!validate(i)) valid = false;
        msg.className = "alert-box " + (valid ? "success" : "error");
        msg.innerHTML = valid
            ? "<strong>Registration submitted successfully!</strong> " + fields[0].value.trim() + " (" + fields[1].value.trim() + ") &bull; " + fields[3].value + "<br><small>Frontend demonstration only; no data was saved.</small>"
            : "Please fix the highlighted errors before submitting.";
        msg.hidden = false;
        if (!valid) fields.find(function (f) { return f.classList.contains("invalid"); }).focus();
    });
    reset.addEventListener("click", function () {
        form.reset(); for (var i = 0; i < fields.length; i++) clearError(i); strength.textContent = "Strength: None";
        strength.className = ""; bar.className = "bar"; msg.hidden = true;
    });
});