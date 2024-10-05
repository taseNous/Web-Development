const validation = new JustValidate("#signup");

validation
    .addField("#username", [
        {
            rule: "required"
        }
    ])
    .addField("#first_name", [
        {
            rule: "required"
        }
    ])
    .addField("#last_name", [
        {
            rule: "required"
        }
    ])
    .addField("#phone", [
        {
            rule: "required"
        }
    ])
    .addField("#latitude", [
        {
            rule: "required"
        }
    ])
    .addField("#longitude", [
        {
            rule: "required"
        }
    ])
    .addField("#password", [
        {
            rule: "required"
        },
        {
            rule: "password"
        }
    ])
    .addField("#password_confirmation", [
        {
            validator: (value, fields) => {
                return value === fields["#password"].elem.value;
            },
            errorMessage: "Passwords should match"
        }
    ])
    .onSuccess((event) => {
        document.getElementById("signup").submit();
    });