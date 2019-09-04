import csv
import re
from tempfile import mkstemp
from shutil import move
from os import fdopen, remove

# From https://stackoverflow.com/questions/39086/search-and-replace-a-line-in-a-file-in-python
def replace(file_path, pattern, subst):
    #Create temp file
    fh, abs_path = mkstemp()
    with fdopen(fh,'w') as new_file:
        with open(file_path) as old_file:
            for line in old_file:
                new_file.write(line.replace(pattern, subst))
    #Remove original file
    remove(file_path)
    #Move new file
    move(abs_path, file_path)


def process(line):
    line = re.sub('\"','',line)
    return line

# The branded food products database
# File names
dcd_file = "BFPD_csv_07132018/Derivation_Code_Description.csv"
nutrients_file = "BFPD_csv_07132018/Nutrients.csv"
products_file = "BFPD_csv_07132018/Products.csv"
servings_file = "BFPD_csv_07132018/Serving_size.csv"
results = "bp.csv"

items = {}
nutrients = {}

with open(products_file, newline='') as f:
    csvreader = csv.reader(f, delimiter = ",")
    num_items = 0
    for row in csvreader:
        num_items += 1
        if(num_items != 1):
            items[int(row[0])] = [int(row[0]),process(str(row[1]).title()),row[3],1,"g"]

with open(servings_file, newline = '') as f:
    csvreader = csv.reader(f, delimiter = ",")
    num_items = 0
    for row in csvreader:
        num_items += 1
        if(num_items != 1):
            try:
                items[int(row[0])][3] = float(row[1])/100
                items[int(row[0])][4] = row[2]
            except:
                continue

"""
    The values are for the quantities found in 100gms or 100 ml
    calories, carbs, sugars, dietary fiber, soluble fiber, inSoluble fiber, protein, total_fat, sodium, cholestrol,
    vitaminA,vitaminB6,vitaminB12,vitaminC,vitaminD,vitaminE,niacin,thiamin,calcium,iron,magnesium
    phosphorus,potassium,riboflavin,zinc
"""

pattern = [208,205,269,291,295,297,203,204,307,601,318,415,418,401,324,340,406,404, 301,303,304,305,306,405,309]

with open(nutrients_file,newline = '') as f:
    csvreader = csv.reader(f, delimiter = ",")
    num_items = 0
    
    for i, line in enumerate(csvreader):
        if(i != 0):
            if(int(line[0]) not in nutrients):
                nutrients[int(line[0])] = [0]*25
        try:
            if(int(line[1]) in pattern):
                nutrients[int(line[0])][pattern.index(int(line[1]))] = float(line[4])
        except:
                print(line)


the_set = set()

with open(results, "w") as output:
        writer = csv.writer(output, delimiter ='~',lineterminator='\n')
        for each_num in nutrients.keys():
            line = items[each_num]
            line += nutrients[each_num]
            the_set.add(len(line))
            writer.writerow(line)

print(the_set)
print("Num records: " + str(len(nutrients)))

replace(results,"\"","")

schema = "(foodID,name,upc,serving_size,quantity_units,calories,total_carbs,sugars,dietary_fiber,soluble_fiber,insoluble_fiber,protein,total_fat,sodium,cholesterol,vitaminA,vitaminB6,vitaminB12,vitaminC,vitaminD,vitaminE,niacin,thiamin,calcium,iron,magnesium,phosphorus,potassium,riboflavin,zinc)"
schema_format = "(%u,%s,%s,%f,%s,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f,%f)"
