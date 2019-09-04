"""
    <?php
    $command = escapeshellcmd('python recommendations.py user-ids');
    $output = shell_exec($command);
    echo $output;
    ?>
    
    Code like snippet the above can be used to extract the recommended recipes. Simply printing
    the id's of recommended recipes is sufficient. The input-id is given as the first command
    line argument
    
    Our recommendations are based on the following nutrients:
    calories,total_carbs,sugar,protein,total_fat,sodium,cholesterol
    
"""

import sys
import math
import numpy as np
import mysql.connector as mysql

db_user = "root"
db_password = "carbfax411"
db = "411_project_db"
user_id = sys.argv[1] # The id of the user as a string

# Create a cursor that connects to the database, an execute always returns a list
connection = mysql.connect(user=db_user, password=db_password, database=db)
cursor = connection.cursor(buffered=True)
connection2 = mysql.connect(user=db_user, password=db_password, database=db)
cursor2 = connection2.cursor(buffered=True)

# We enclose it in a try-catch block for efficient error handling
try:
    daily_calories = 0
    daily_carbs = 0
    daily_sugar = 0
    daily_fat = 0
    daily_protein = 0
    daily_sodium = 0
    daily_cholesterol = 0
    
    # Retrieve nutrient aggregates from the recipes table
    query = ("SELECT SUM(R.calories * A.quantity),SUM(R.total_carbs * A.quantity),SUM(R.sugar * A.quantity) "
     ",SUM(R.protein * A.quantity),SUM(R.total_fat * A.quantity),SUM(R.sodium * A.quantity),SUM(R.cholesterol * A.quantity) "
     "FROM recipes AS R, ate as A WHERE R.foodID = A.foodID AND A.username = %s;")
    cursor.execute(query,(user_id,))
    
    for (cal,carbs,s,p,f,sod,c) in cursor:
        
        if(cal):
            daily_calories += float(cal)
        if(carbs):
            daily_carbs += float(carbs)
        if(s):
            daily_sugar += float(s)
        if(p):
            daily_protein += float(p)
        if(f):
            daily_fat += float(f)
        if(sod):
            daily_sodium += float(sod)
        if(c):
           daily_cholesterol += float(c)
     

    # Retrieve nutrient aggregates from the products table
    query = ("SELECT SUM(P.calories * A.quantity),SUM(P.total_carbs * A.quantity),SUM(P.sugars * A.quantity) "
     ",SUM(P.protein * A.quantity),SUM(P.total_fat * A.quantity),SUM(P.sodium * A.quantity),SUM(P.cholesterol * A.quantity) "
     "FROM products AS P, ate as A WHERE P.foodID = A.foodID AND A.username = %s;")
    cursor.execute(query,(user_id,))

    for (cal,carbs,s,p,f,sod,c) in cursor:

        if(cal):
            daily_calories += float(cal)
        if(carbs):
            daily_carbs += float(carbs)
        if(s):
            daily_sugar += float(s)
        if(p):
            daily_protein += float(p)
        if(f):
            daily_fat += float(f)
        if(sod):
            daily_sodium += float(sod)
        if(c):
            daily_cholesterol += float(c)

    # Retrieve the list of unique dates to compute a per-day average
    query = ("SELECT COUNT(DISTINCT (EXTRACT(DAY FROM date))) FROM ate WHERE username = %s;")
    cursor.execute(query,(user_id,))
    
    for (days,) in cursor:
        num_days = int(days)

    if(num_days == 0):
        num_days = 1

    daily_calories /= num_days
    daily_carbs /= num_days
    daily_sugar /= num_days
    daily_protein /= num_days
    daily_fat /= num_days
    daily_sodium /= num_days
    daily_cholesterol /= num_days

   # Retrieve the targets if applicable
    query = ("SELECT calorie_target,carb_target,fat_target,protein_target FROM users "
    "WHERE username = %s;")
    cursor.execute(query,(user_id,))

    cal_target = 0
    carb_target = 0
    fat_target = 0
    prot_target = 0

    for (c1,c2,f,p) in cursor:

        if c1 is not None:
            cal_target = c1
        if c2 is not None:
            carb_target = c2
        if f is not None:
            fat_target = f
        if p is not None:
            prot_target = p

    query = "SELECT age,height,weight FROM users WHERE username = %s;"
    cursor.execute(query,(user_id,))

    age = 0
    height = 0
    weight = 0
    for (a,h,w) in cursor:
        age = float(a)
        height = float(h)*2.54
        weight = float(w)*0.453592;


    cals_recmnd = math.ceil((10 * weight) + (6.25 *height) - (5 * age) + 5);
    carbs_recmnd = math.ceil(cals_recmnd * 0.5 / 4);
    prot_recmnd = math.ceil(cals_recmnd * 0.25 / 4);
    fat_recmnd = math.ceil(cals_recmnd * 0.25 / 9);

    if(carb_target == 0):
        carb_target = carbs_recmnd

    if(fat_target == 0):
        fat_target = fat_recmnd

    if(prot_target == 0):
        prot_target = prot_recmnd

    # Based on average estimates
    # The calorie target aspect is taken
    sod_target = 2500
    cholesterol_target = 250


    # Retrieve a list of potential recipes i.e. recipes that the user has not consumed
    query = ("SELECT foodID,name,calories,total_carbs,sugar,protein,total_fat,sodium,cholesterol "
    "FROM recipes WHERE recipes.foodID NOT IN "
    "(SELECT foodID from ate WHERE username = %s);")
    cursor.execute(query,(user_id,))
    recipes = []

    for (id,name,c,tc,s,p,f,sod,ch) in cursor:
        recipes.append([id,name,float(c),float(tc),float(s),float(p),float(f),float(sod),float(ch)])


    # We recommend those recipes that minimize the mean-squared distance between the various nutrient values and recommendations
    # We assume that the standard portion is about 500 calories and multiply all the values o
    # This computes the Macro-Nutrient Score
    recommendations = []
    for recipe in recipes:
        
        factor = 0.5
        if(recipe[2] != 0):
            factor = 500/recipe[2] #Factor to standardize all recipes

        diff = (0.15*(fat_target/4 - recipe[7]*factor)**2 + 0.15*(prot_target/4 - recipe[6]*factor)**2 + 0.15*(sod_target/4 - recipe[6]*factor)**2 + 0.15*(cholesterol_target/4 - recipe[8]*factor)**2 + 0.15*(carb_target/4 - recipe[4]*factor)**2)

        recommendations.append((diff,recipe[0],recipe[1]))

    recommendations.sort(key = lambda tup: tup[0])

    top_recommendations = recommendations[:250]

    # From the top 100 macro recommendations, we select the top 5 recipes based on micro-nutrient information from the contains table

    query1 = ("SELECT product_foodID,quantity,volume,weight FROM contains "
              "WHERE contains.recipe_foodID = %s;")
        
    query2 = ("SELECT vitaminA, vitaminB6,vitaminB12,vitaminC,vitaminD,vitaminE,niacin, "
              "thiamin,calcium,iron,magnesium,phosphorus,potassium,riboflavin,zinc "
              "FROM products WHERE products.foodID = %s;")

    top_recommendations_micro = []
    for recipe in top_recommendations:
        cursor.execute(query1,(int(recipe[1]),))

        ingredient_total = 0
        for ingredient in cursor:
            cursor2.execute(query2,(int(ingredient[0]),))

            for ingredient_val in cursor2:

                ingredient_total += sum(ingredient_val[:8]) + 0.5*(sum(ingredient_val[8:]))

        top_recommendations_micro.append((recipe[0],ingredient_total,0,recipe[1],recipe[2]))
                
    max_diff = max(top_recommendations_micro,key=lambda item:item[0])[0]
    max_tot = max(top_recommendations_micro,key=lambda item:item[1])[1]

    # The weighting ensures that the algorithm provides some variations in its recommendations, not just the outliers in the dataset
    top_recommendations_micro = map(lambda x:(x[0]/max_diff, x[1]/max_tot, 0 , x[3] , x[4]), top_recommendations_micro)
    top_recommendations_micro = map(lambda x:(x[0], x[1], 0.5*x[1] - 0.5*x[0], x[3], x[4]), top_recommendations_micro)
    top_recommendations_micro.sort(key = lambda tup: tup[2])

    res = ""
    num = 0
    the_set = set()
    index_set = set()
    # We shuffle the recipes a little to remove outliers

    while num < 5:
        i = np.random.randint(50)
        if(top_recommendations_micro[i][4] not in the_set and i not in index_set):
            res += str(top_recommendations_micro[i][3]) + ","
            the_set.add(top_recommendations_micro[i][2])
            index_set.add(i)
            num += 1

    print(res[:-1])

#    for i in range(10):
#        print(top_recommendations_micro[i][4])
#
#    print(daily_calories)
#    print(daily_carbs)
#    print(daily_sugar)
#    print(daily_protein)
#    print(daily_fat)
#    print(daily_cholesterol)
#    print("Done!")

    connection.close()


except mysql.Error as e:
    print("Error: {}".format(e))


