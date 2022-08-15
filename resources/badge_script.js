var instance = 0;

function newField() {
    var old_id = "badge_form-0";

    if (instance == 0) {
    } else {
        // get id of last visible form
        var last_id = document
            .getElementsByClassName("badge-form")
        [document.getElementsByClassName("badge-form").length - 1].id.match(
            /-([0-9]*)$/
        )[1];

        old_id = "badge_form-" + last_id;
    }

    // Create clone
    console.log(old_id);
    var clone = document.getElementById(old_id).cloneNode(true);
    var input_fields = clone.getElementsByClassName("badge-field-input");

    instance++;

    // clean notices from current
    Array.from(document.querySelectorAll('#empty-field-note-text')).forEach(function(el) { 
        el.remove();
    });

    Array.from(document.querySelectorAll('.empty-field-note')).forEach(function(el) { 
        el.classList.remove('empty-field-note');
    });

    // and in clone
    Array.from(clone.querySelectorAll('#empty-field-note-text')).forEach(function(el) { 
        el.remove();
    });

    Array.from(clone.querySelectorAll('.empty-field-note')).forEach(function(el) { 
        el.classList.remove('empty-field-note');
    });
    

    //Make children unique
    // input fiedlds
    for (let i = 0; i < input_fields.length; i++) {
        input_fields[i].id = input_fields[i].id.replace(/-.*$/, "-" + instance);

        // Clear values
        if (input_fields[i].type == "text") {
            input_fields[i].value = "";
        }
        input_fields[i].checked = false;
    }

    // id of remove button
    clone.getElementsByClassName("remove-form-button")[0].id = clone
        .getElementsByClassName("remove-form-button")[0]
        .id.replace(/-.*$/, "-" + instance);

    // id of form
    clone.id = clone.id.replace(/-.*$/, "-" + instance);

    // Clone home location - after old..
    var insertHere = document.getElementById(old_id);
    insertHere.parentNode.insertBefore(clone, insertHere.nextSibling);
    document
        .getElementById("remove_form_button-" + instance)
        .addEventListener("click", removeForm);

    // show remove field button
    for (
        var i = 0;
        i < document.getElementsByClassName("remove-form-button").length;
        i++
    ) {
        document.getElementsByClassName("remove-form-button")[i].style.visibility =
            "visible";
    }
}

function httpGet(theUrl) {
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", theUrl, false); // false for synchronous request
    xmlHttp.send(null);
    return xmlHttp.responseText;
}

const notificationDiv = document.createElement('div');
notificationDiv.className = 'empty-field-note-text';
notificationDiv.id = 'empty-field-note-text';
notificationDiv.innerHTML = `
    <p>Please fill this field</p>
`;

function attachEmptyFieldNote(element, toggle)
{
    if(toggle){
        element.classList.add("empty-field-note");
        if (document.getElementById('empty-field-note-text') == null)
        {
            element.appendChild(notificationDiv);
        }
        

    } else {
        element.classList.remove("empty-field-note");
        if (document.getElementById('empty-field-note-text') != null)
        {
            document.getElementById('empty-field-note-text').remove();
        }
    }
}

// Returns built url if all fields filled
//  false if field is empty
function buildURLs(product_id) {
    var base_url = "/?add-to-cart=" + product_id;
    var all_forms = document.getElementsByClassName("badge-form");
    var url_list = [];

    for (let j = 0; j < all_forms.length; j++) {
        // list of fields
        var input_fields = all_forms[j].getElementsByTagName("input");
        var select_fields = all_forms[j].getElementsByTagName("select");
        var radio_group = all_forms[j].getElementsByClassName("radio-options");
        var url = base_url;

        // iterate over each form
        for (let i = 0; i < input_fields.length; i++) {
            // Only input fields
            if (input_fields[i].classList.contains("badge-field-input")) {
                var field = input_fields[i];
                if (field.type == "text") {
                    // Handle text field
                    if (field.value != "") {
                        url += "&" + field.getAttribute("field") + "=" + field.value;
                        attachEmptyFieldNote(field.parentElement, false);
                        
                    } else {
                        // Handle empty
                        // Issue notification
                        attachEmptyFieldNote(field.parentElement, true);
                        return [false];
                    }
                }
            }
        }

        for (let i = 0; i < select_fields.length; i++) {
            if (select_fields[i].classList.contains("badge-field-input")) {
                var field = select_fields[i];

                if (field.value != "") {
                    url += "&" + field.getAttribute("field") + "=" + field.value;
                    attachEmptyFieldNote(field.parentElement, false);
                } else {
                    // Issue notification
                    attachEmptyFieldNote(field.parentElement, true);
                    return [false];
                }
            }
        }

        // for each radio group in form check for a checked field
        for (let i = 0; i < radio_group.length; i++) {
            // For each radio option in group
            let is_group_checked = false;
            let group = radio_group[i].getElementsByTagName('input');
            for (let k = 0; k < group.length; k++) {
                let field = group[k];
                console.log('field');
                if (field.checked) {
                    is_group_checked = true;
                    url += "&" + field.getAttribute("field") + "=" + field.value;
                    attachEmptyFieldNote(field.parentElement.parentElement.parentElement, false);
                    break;
                }
            }

            if (!is_group_checked) {
                // If we get here, there has not been a selection within some group
                // issue notification
                attachEmptyFieldNote(radio_group[i].parentElement, true);
                return [false];
            }
        }
        url_list.push(url);
    }

    if (url_list.length <= 0) {
        return [false];
    } else {
        return [true, url_list];
    }
}

function addToCart(product_id, cart_url) {
    // for each url in urls
    var urls = buildURLs(product_id);
    if (urls[0]) {
        urls[1].forEach(function(el) { 
            console.log("Sending: " + el);
            httpGet(el);
        });
        // redirect to cart page only if all
        window.location.href = cart_url;
    }
}

function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
    }
}

function removeForm(event) {
    if (document.getElementsByClassName("badge-form").length > 1) {
        console.log(event.target.id);

        var instance_id = event.target.id.match(/-([0-9]*)$/)[1];

        console.log(instance_id);

        form_id = "badge_form-" + instance_id;
        document.getElementById(form_id).remove();
    }

    if (document.getElementsByClassName("badge-form").length == 1) {
        document.getElementsByClassName("remove-form-button")[0].style.visibility =
            "hidden";
    }

}

document
    .getElementById("remove_form_button-0")
    .addEventListener("click", removeForm);
