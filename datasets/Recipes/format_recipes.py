import csv

# File names

recipe_file = "Data/recipes.csv"
ingredients_file = "Data/quantified_ingredients.csv"
bp_file = "Data/bp.csv"
output_file = "Data/formatted_recipes.csv"
recipe_id_offset = 5000000

bp_schema = "(foodID,name,upc,serving_size,quantity_units,calories,total_carbs,sugars,dietary_fiber,soluble_fiber,insoluble_fiber,protein,total_fat,sodium,cholesterol,vitaminA,vitaminB6,vitaminB12,vitaminC,vitaminD,vitaminE,niacin,thiamin,calcium,iron,magnesium,phosphorus,potassium,riboflavin,zinc)"

raw_ingredients = {}
# Calories,Carbs,Sugars,Protein,Fat,Sodium,Cholestrol
# 5,6,7,11,12,13,14
with open(bp_file, "r") as f:
    ingredients_reader = csv.reader(f, delimiter="~")
    for ingredient in ingredients_reader:
        raw_ingredients[int(ingredient[0])] = [ingredient[5],ingredient[6],ingredient[7],
                                          ingredient[11],ingredient[12],ingredient[13],ingredient[14]]

recipes = {}
with open(recipe_file,"r") as f:
     recipe_reader = csv.reader(f, delimiter="~")
     for recipe in recipe_reader:
         recipes[int(recipe[0])] = [recipe[0],recipe[1],0,0,0,0,0,0,0]

with open(ingredients_file) as f:
    ingredients_reader = csv.reader(f, delimiter="~")
    
    for ingredient in ingredients_reader:
        try:
            multiplier = 1
            recipe = int(ingredient[0])

            if(float(ingredient[4]) != -1):
                multiplier = float(ingredient[4])
            if(float(ingredient[5]) != -1):
                multiplier = float(ingredient[5])
            
            ingredient = raw_ingredients[int(ingredient[1])]
            for i in range(7):
                recipes[recipe][i + 2] += int((float(multiplier)) * float(ingredient[i]))

        except Exception as e:
            print(e)


with open(output_file, "w") as f:
    writer = csv.writer(f, delimiter="~", lineterminator='\n')
    for recipe in recipes.keys():
        recipe = recipes[recipe]
        writer.writerow(recipe)
