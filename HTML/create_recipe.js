// search food_item to add
function search_food() {
    var search_option = "product";
    if ( document.getElementById('_recipe').checked ) {
        search_option = "recipe";               
    }
    var search_name = $("#food_search").val();
    $.ajax({
        cache: false,
        url: "food_search.php",
        data: "name=" + search_name + "&option=" + search_option,
        success: function(data) {
            $("#food_suggestion").html(data);
        }
    })
}         
$("#food_search").bind("keyup mouseenter", search_food);
$('input[name="search_option"]').on('click change', search_food);

//---------------------------------------------
// Display the item selected so that users don't have to enter item id on their own
$(document).on("click", ".food_search_item", function(event) {
    div_elem = "";
    if (event.target.id === "") {
        div_elem = event.target.parentNode.id
    }
    else {
        div_elem = event.target.id
    }
    param_arr = div_elem.split("*")
    item_name = param_arr[0]
    item_id = param_arr[1]
    $("#item_selected_text").html(item_name.replace(/_/g, " ") + "* " + item_id.replace(/_/g, " "))
})

//---------------------------------------------
// item_arr stores food items
// each food item is also an array; 0-index: name, 1: food id, 2: quantity unit, 3: quantity, 4: description
// all values in each food item are strings
var item_arr = []

function display_one_item(item) {
    var s ="<tr>" + 
           "<td>" + item[0] + "</td>" + 
           "<td>" + item[1] + "</td>" + 
           "<td>" + item[2] + "</td>" + 
           "<td>" + item[3] + "</td>" + 
           "<td>" + 
                "<button name='remove' class='btn btn-sm btn-primary btn-block recipe_item_minus_button' type='submit' id=" + item[1] + ">-</button>" + 
           "</td>" + 
           "</tr>"
    return s
}

function display_items() {
    var html = ""
    for (var i=0; i < item_arr.length; i++) {
        html += display_one_item(item_arr[i])
    }
    $("#items_added_content").html(html)
}

// Add the item selected to the right display area
$(document).on("click", '#_add_item_button', function(event) {
    var item = []

    var name_and_id = $("#item_selected_text").html()
    if ( !name_and_id ) {
        return
    }
    name_and_id = name_and_id.split("*")
    item.push(name_and_id[0])
    item.push(name_and_id[1].slice(1))

    var quantity_unit = "measurement_std"
    if ( document.getElementById("_volume").checked ) {
        quantity_unit = "volume"    
    }
    else if ( document.getElementById("_weight").checked ) {
        quantity_unit = "weight"    
    }
    item.push(quantity_unit)

    var quantity = $("#quantity_input").val()
    if ( !quantity ) {
        return
    }
    item.push(quantity)

    item_arr.push(item)
    display_items()
})

// Remove item
$(document).on('click', ".recipe_item_minus_button",  function(event) {
    const id = event.target.id
    var index = 0
    for (var i=0; i < item_arr.length; i++) {
        if (item_arr[i][1] === id) {
            index = i
            break
        }    
    }
    item_arr.splice(i, 1)
    display_items()
 }
)

//---------------------------------------------------------------
//var recipe_description = $("#recipe_description_input").val() || " "
//item.push(recipe_description)

// Submit recipe button   
$(document).on("click", "#submit_recipe_btn", function(event) {
    var recipe_name = $("#recipe_name_input").val()
    var recipe_description = $("#recipe_description_input").val()

    if (item_arr.length === 0) {
        $("#recipe_added_msg").html("Please add at least one item")
    }
    else if (recipe_name === "") {
        $("#recipe_added_msg").html("Please enter the recipe name")
    }
    else {
        //console.log("line 125")
        $.ajax({
            cache: false,
            url: "get_new_recipe_id.php",
            data: "",
            success: function(data) {
                var new_recipe_id = data
                var data_str = "recipe_name=" + recipe_name + "&" + 
                               "recipe_description=" + recipe_description + "&" +
                               "new_recipe_id=" + new_recipe_id
                $.ajax({
                    cache: false,
                    url: "update_recipes.php",
                    data: data_str,
                    success: function(rv) {
                        //console.log(rv)
                        var done_count = 0
                        $.each(item_arr, function(key, value) {
                            var _data = "recipe_name=" + recipe_name + "&" + 
                                        "recipe_description=" + recipe_description + "&" +
                                        "item_name=" + value[0] + "&" +
                                        "item_id=" + value[1] + "&" +
                                        "quantity_unit=" + value[2] + "&" +
                                        "quantity=" + value[3] + "&" + 
                                        "new_recipe_id=" + new_recipe_id
                            $.ajax({
                                cache: false,
                                url: "update_contains.php",
                                data: _data,
                                success: function(data) {            
                                    done_count += 1
                                    //console.log(data)
                                    //console.log(_data, done_count)
                                    //console.log(item_arr)
                                    if (done_count === item_arr.length) {
                                        //console.log("async????????????????")
                                        $("#recipe_added_msg").html("Recipe Added")
                                        $("#item_selected_text").html("")
                                        $("#items_added_content").html("")
                                        $("#recipe_name_input").val("")
                                        $("#recipe_description_input").val("")
                                        item_arr = []
                                    }
                                }
                            })
                        })     
                    }
                })  
            }
        })
    }
})