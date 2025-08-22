<style>
.aside {
    background-color: #3383d4;
    width: 200px;
    height: 100vh;
    color: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.logo {
    margin-top: 20px;
    margin-bottom: 40px;
}

.logo img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.menu {
    width: 100%;
}

.select {
    width: 100%;
    padding: 15px 20px;
    transition: background 0.3s;
}

.select a {
    text-decoration: none;
    color: white;
    font-size: 16px;
    display: flex;
    align-items: center;
}

.select i {
    margin-right: 10px;
    font-size: 18px;
}

.select:hover {
    background-color: #2c6cb5;
    cursor: pointer;
}
</style>
<div class="aside">
    <div class="logo">
        <img src="../images/logo.png" alt="" srcset="">
    </div>

    <div class="menu">
        <div class="select">
            <a href="dashbord.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </div>
        <div class="select">
            <a href="manageProjects.php">
                <i class="fas fa-folder-open"></i> Projects
            </a>
        </div>
        <div class="select">
            <a href="manageTask.php">
                <i class="fas fa-tasks"></i> Tasks
            </a>
        </div>
        <div class="select">
            <a href="managedoto.php">
                <i class="fas fa-list-check"></i> To Do
            </a>
        </div>
        <div class="select">
            <a href="">
                <i class="fas fa-user"></i> Profile
            </a>
        </div>
    </div>
</div>