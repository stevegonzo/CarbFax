import csv
import difflib

# File names

recipe_file = "Data/matched_ingredients.csv"
output_file = "Data/quantified_ingredients.csv"

def represents_int(s):
    try:
        int(s)
        return True
    except ValueError:
        return False


def convert_string_to_float(str):
    num = 0
    parts = str.split()
    db_parts = parts[0].split("/")
    if (len(db_parts) == 1):
        num += float(db_parts[0])
    else:
        num += float(db_parts[0]) / float(db_parts[1])

    if (len(parts) == 2):
        double_parts = parts[1].split("/")
        if (len(double_parts) == 2):
            num += float(double_parts[0]) / float(double_parts[1])
    return num

with open(recipe_file) as f1, open(output_file, "w") as output:
    recipe_reader = csv.reader(f1, delimiter="~")
    writer = csv.writer(output, delimiter="~", lineterminator='\n')
    volume_names = ["drop", "dr", "gt", "gtt", "dr.", "gt.", "gtt.", "smidgen", "smdg", "smi", "smdg.", "smi.", "pinch", "pn", "pn.", "dash", "ds", "ds.", "saltspoon", "scruple", "ssp", "ssp.", "coffeespoon", "csp", "csp.", "fluid dram", "fldr", "fl dr", "fl.dr.", "teaspoon", "tsp", "t", "tsp.", "t.", "dessertspoon", "dsp", "dssp", "dstspn", "dsp.", "dssp.", "dstspn.", "tablespoon", "tbsp", "tbsp.", "fluid ounce", "fl oz", "fl.oz", "wineglass", "wgf", "wgf.", "gill", "teacup", "tcf", "tcf.", "cup", "c", "c.", "pint", "pt", "pt.", "quart", "qt", "qt.", "pottle", "pot", "pot.", "gallon", "gal", "gal.", "milliliter", "milliliters", "ml", "ml."];
    volume_conversions = [0.0513429, 0.0513429, 0.0513429, 0.0513429, 0.0513429, 0.0513429, 0.0513429, 0.115522, 0.115522, 0.115522, 0.115522, 0.115522, 0.231043, 0.231043, 0.231043, 0.462086, 0.462086, 0.462086, 0.924173, 0.924173, 0.924173, 0.924173, 1.84835, 1.84835, 1.84835, 3.69669, 3.69669, 3.69669, 3.69669, 4.92892, 4.92892, 4.92892, 4.92892, 4.92892, 9.85784, 9.85784, 9.85784, 9.85784, 9.85784, 9.85784, 9.85784, 14.7868, 14.7868, 14.7868, 29.5735, 29.5735, 29.5735, 59.1471, 59.1471, 59.1471, 118.294, 118.294, 118.294, 118.294, 236.588, 236.588, 236.588, 473.176, 473.176, 473.176, 946.353, 946.353, 946.353, 1892.71, 1892.71, 1892.71, 3785.41, 3785.41, 3785.41, 1, 1, 1, 1];
    weight_names = ["gram", "grams", "gr", "gr.", "ounce", "ounces", "oz", "oz.", "etto", "pound", "pounds", "lb", "lbs", "lb.", "lbs.", "kilogram", "kilograms", "kilo", "kilos", "kg", "kilo.", "kg.", "milligram", "milligrams", "mg", "mgs", "mg.", "mgs."]
    weight_conversions = [1, 1, 1, 1, 28, 28, 28, 28, 100, 454, 454, 454, 454, 454, 454, 1000, 1000, 1000, 1000, 1000, 1000, 1000, 0.001, 0.001, 0.001, 0.001, 0.001, 0.001]

    # print(len(volume_names), len(volume_conversions))
    # print(len(weight_names), len(weight_conversions))

    for recipe_item in recipe_reader:
        try:
            curr_quantity = convert_string_to_float(recipe_item[2])
            curr_measurement = recipe_item[3].lower()

            # Correct quantity
            recipe_item[2] = curr_quantity

            # Check volumes
            flag = False
            for i in range(len(volume_names)):
                if (curr_measurement == volume_names[i]):
                    recipe_item.append(curr_quantity * volume_conversions[i] / 100)
                    flag = True
            if flag == False:
                recipe_item.append(-1)

            # Check weights
            flag = False
            for i in range(len(weight_names)):
                if (curr_measurement == weight_names[i]):
                    recipe_item.append(curr_quantity * weight_conversions[i] / 100)
                    flag = True
            if flag == False:
                recipe_item.append(-1)

            writer.writerow(recipe_item)

        except Exception as e:
            print(e)

