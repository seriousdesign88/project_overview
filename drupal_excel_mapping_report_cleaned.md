## 📦 Drupal Product Fields
| Field Name | Field Type | Required | Description |
|------------|------------|----------|-------------|
| attribute_color commerce_product_variation | entity_reference | No | Color attribute |
| attribute_length commerce_product_variation | entity_reference | No | Length attribute |
| attribute_material commerce_product_variation | entity_reference | No | Material attribute |
| attribute_surface commerce_product_variation | entity_reference | No | Surface attribute |
| feeds_item commerce_product_variation | feeds_item | No | Feeds integration |
| field_downloads commerce_product_variation | entity_reference_revisions | No | Related downloadable documents |
| field_drawing_no commerce_product_variation | string | No | Drawing number |
| field_dxf_files commerce_product_variation | file | No | DXF file attachments |
| field_equipment commerce_product_variation | list_string | No | Equipment type |
| field_extension commerce_product_variation | physical_measurement | No | Extension length |
| field_in_stock commerce_product_variation | boolean | No | Availability flag |
| field_length_for_filter commerce_product_variation | physical_measurement | No | Length for filtering |
| field_load_capacity commerce_product_variation | physical_measurement | No | Load capacity |
| field_packaging_type commerce_product_variation | list_string | No | Packaging type |
| field_packaging_unit commerce_product_variation | integer | No | Packaging quantity |
| field_part_no commerce_product_variation | string | No | Part number |
| field_product_details_for_filter commerce_product_variation | string | No | Additional details for filtering |
| field_step_files commerce_product_variation | file | No | STEP file attachments |
| field_technical_sheet_in commerce_product_variation | file | No | Technical sheet in inches |
| field_technical_sheet_mm commerce_product_variation | file | No | Technical sheet in mm |
| field_weight commerce_product_variation | physical_measurement | No | Weight |

## 📊 Excel Fields Overview
| Excel Column | Translation (guess) |
|--------------|---------------------|
| Code
Sliding Systems | Code |
| Type Sliding Systems | Type |
| Unnamed: 2 |  |
| Kürzel | Abbreviation |
| Ausgeschrieben | Written Out |
| Zeichnung | Drawing |
| Kontrolle | Check |
| Typenbezeichnung | Type |
| Kurzbezeichnung | Drawing |
| Length
 | Length |
| Extension
 |  |
| Capacity
 |  |
| Weight 
(kg) |  |
| Total Length 
(mm) | Length |
| Width (mm) |  |
| Height 
(mm) |  |
| Description |  |
| Hersteller |  |
| country of manufacture (origin) |  |
| Packaging unit (1=1slide, a pair are 2 slides) |  |
| Customs tariff number |  |
| Installation
Length: L | Length |
| Extension
Length: D | Length |
| Load per
pair: kg | Load Capacity |
| Maß "X" Oder Load cap Z bei FL&FZ | Load Capacity |
| Maß "Y" Oder Load cap Y bei FL&FZ | Load Capacity |
| "C" |  |
| number of drillings (FZ) |  |

## 🔁 Field Mapping
| Excel Column | Mapped Drupal Field |
|--------------|---------------------|
| Code
Sliding Systems | field_in_stock commerce_product_variation |
| Type Sliding Systems | field_in_stock commerce_product_variation |
| Unnamed: 2 |  |
| Kürzel |  |
| Ausgeschrieben |  |
| Zeichnung |  |
| Kontrolle |  |
| Typenbezeichnung |  |
| Kurzbezeichnung |  |
| Length
 | field_length_for_filter commerce_product_variation |
| Extension
 |  |
| Capacity
 |  |
| Weight 
(kg) |  |
| Total Length 
(mm) | field_length_for_filter commerce_product_variation |
| Width (mm) |  |
| Height 
(mm) |  |
| Description |  |
| Hersteller |  |
| country of manufacture (origin) | field_in_stock commerce_product_variation |
| Packaging unit (1=1slide, a pair are 2 slides) | field_in_stock commerce_product_variation |
| Customs tariff number |  |
| Installation
Length: L | field_in_stock commerce_product_variation |
| Extension
Length: D | field_length_for_filter commerce_product_variation |
| Load per
pair: kg | field_load_capacity commerce_product_variation |
| Maß "X" Oder Load cap Z bei FL&FZ | field_load_capacity commerce_product_variation |
| Maß "Y" Oder Load cap Y bei FL&FZ | field_load_capacity commerce_product_variation |
| "C" |  |
| number of drillings (FZ) | field_in_stock commerce_product_variation |