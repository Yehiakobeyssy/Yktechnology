<?php
    $sql=$con->prepare('SELECT Fname,MidelName,LName,Staff_Photo FROM  tblstaff WHERE staffID = ?');
    $sql->execute(array($staff_Id));
    $result = $sql->fetch();
    $staff_name = $result['Fname'].' '.$result['MidelName'].' '.$result['LName'];
    $staff_photo = $result['Staff_Photo'];

    $photoPath = "../Documents/" . $staff_photo;
    if (!file_exists($photoPath) || empty($personal_Photo)) {
        $photoPath = "../Documents/nophoto.png";
    }
?>
<style>
.header {
    width: 100%;
    padding: 10px;
    background-color: #1c4e80;
    display: flex;
    justify-content: flex-end;
    color: white;
    box-shadow: 4px 0px 30px 0px rgba(245, 107, 14, 0.04);
}

.profilename {
    display: flex;
    align-items: center;
    position: relative;
}

.profilename img {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
}

.staff-name {
    margin-right: 8px;
    font-weight: bold;
}

.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: white;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background:  #1c4e80;
    border: 1px solid #ddd;
    color: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    border-radius: 4px;
    display: none;
    min-width: 150px;
    z-index: 999;
}

.dropdown-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: white;
}

.dropdown-menu a:hover {
    background-color: #3383d4;
}
</style>
<div class="header">
    <div class="profilename">
        <img src="<?php echo $photoPath ?>" alt="">
        <label class="staff-name"><?php echo $staff_name ?></label>
        <div class="dropdown">
            <button class="dropdown-toggle">
                
            </button>
            <div class="dropdown-menu">
                <a href="#">Change Password</a>
                <a href="#">Logout</a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toggle = document.querySelector(".dropdown-toggle");
        const menu = document.querySelector(".dropdown-menu");

        toggle.addEventListener("click", function(e) {
            e.stopPropagation(); 
            menu.style.display = (menu.style.display === "block") ? "none" : "block";
        });

        document.addEventListener("click", function() {
            menu.style.display = "none";
        });
    });
</script>
