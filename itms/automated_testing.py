from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select, WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time


options = Options() 
options.add_experimental_option("detach", True)

service = Service("chromedriver.exe")
driver = webdriver.Chrome(service=service, options=options)

wait = WebDriverWait(driver, 10)

# ================================
# 2. Project URLs
# ================================
signup_url = "http://localhost/itms/signup.php"
login_url = "http://localhost/itms/login.php"
upload_url = "http://localhost/itms/upload_file.php"
add_revenue_url = "http://localhost/itms/add_revenue.php"

# ================================
# 3. Test Data
# ================================
test_user = {
    "name": "testuser",
    "address": "123 Test Lane",
    "email": "testuser10@example.com",
    "phone": "1234567890",
    "password": "TestPass123",
    "role": "taxpayer"
}

sample_pdf_path = "C:/xampp/htdocs/itms/uploads/ml18.pdf"




def test_signup():
    print("\n🚀 Running SIGNUP Test...")
    driver.get(signup_url)


    wait.until(EC.presence_of_element_located((By.NAME, "name")))

    driver.find_element(By.NAME, "name").send_keys(test_user["name"])
    driver.find_element(By.NAME, "address").send_keys(test_user["address"])
    driver.find_element(By.NAME, "email").send_keys(test_user["email"])
    driver.find_element(By.NAME, "phone").send_keys(test_user["phone"])
    driver.find_element(By.NAME, "password").send_keys(test_user["password"])

    role_select = Select(driver.find_element(By.NAME, "role"))
    role_select.select_by_value(test_user["role"])

    driver.find_element(By.TAG_NAME, "button").click()

    time.sleep(2)

    assert "login.php" in driver.current_url, "❌ Signup Failed"
    print("✅ Signup test passed!")


def test_login():
    print("\n🚀 Running LOGIN Test...")
    driver.get(login_url)

    wait.until(EC.presence_of_element_located((By.NAME, "name")))

    driver.find_element(By.NAME, "name").send_keys(test_user["name"])
    driver.find_element(By.NAME, "password").send_keys(test_user["password"])

    driver.find_element(By.TAG_NAME, "button").click()

    time.sleep(2)

    assert "taxpayer.php" in driver.current_url, "❌ Login Failed"
    print("✅ Login test passed!")


def test_add_revenue():
    print("\n🚀 Running ADD REVENUE Test...")
    driver.get(add_revenue_url)

    wait.until(EC.presence_of_element_located((By.NAME, "revenue")))

    driver.find_element(By.NAME, "revenue").send_keys("1000")

    driver.find_element(By.TAG_NAME, "button").click()

    time.sleep(2)

    assert "taxpayer.php" in driver.current_url, "❌ Add Revenue Failed"
    print("✅ Add Revenue test passed!")


def test_upload_file():
    print("\n🚀 Running UPLOAD FILE Test...")
    driver.get(upload_url)

    wait.until(EC.presence_of_element_located((By.NAME, "document")))

    driver.find_element(By.NAME, "document").send_keys(sample_pdf_path)
    driver.find_element(By.TAG_NAME, "button").click()

    time.sleep(2)

    page_text = driver.find_element(By.TAG_NAME, "body").text

    assert "Document uploaded successfully" in page_text, "❌ File upload failed"
    print("✅ File upload test passed!")



try:
    test_signup()
    test_login()
    test_add_revenue()
    # test_upload_file()    # Enable if needed

    print("\n🎉 ALL TESTS COMPLETED SUCCESSFULLY!")

except Exception as e:
    print("\n❌ ERROR OCCURRED:", e) 

finally:
    input("\nPress ENTER to close the browser manually...")
    driver.quit()
