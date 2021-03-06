<?php
include 'connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="CSS/Sidebar.css"/>
    <link rel="stylesheet" href="CSS/iconfont-Sidebar.css"/>
    <title>GlobalGamingStats</title>
</head>

<body>
<div id="Centered">

    <div class="Header">
        <img src="ProjectIMG/Header.jpg"/>
    </div>

    <div class="MainBox">

        <div class="SideBar">
            <ul>
                <li>
                    <a href="Player-Info.php">
                        <span class="iconfont icon-user"></span>
                        <span>Account Info</span>
                    </a>
                </li>
                <li>
                    <a href="Player-Membership.php">
                        <span class="iconfont icon-membership"></span>
                        <span>Membership</span>
                    </a>
                </li>
                <li>
                    <a href="Player-MyGames.php">
                        <span class="iconfont icon-game"></span>
                        <span>My Games</span>
                    </a>
                </li>
                <li>
                    <a href="Player-Bank.php">
                        <span class="iconfont icon-bank"></span>
                        <span>Bank Account</span>
                    </a>
                </li>
                <li>
                    <a href="Player-Device.php">
                        <span class="iconfont icon-device"></span>
                        <span>Device</span>
                    </a>
                </li>
                <li>
                    <a href="Player-Gift.php">
                        <span class="iconfont icon-gift"></span>
                        <span>Gift</span>
                    </a>
                </li>
                <li>
                    <a href="Player-Friends.php">
                        <span class="iconfont icon-friends"></span>
                        <span>Friends</span>
                    </a>
                </li>
                <li>
                    <a href="Player-AllGames.php">
                        <span class="iconfont icon-all"></span>
                        <span>All Games</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="Content">
            <form method="POST" action="GameInfo-Stats.php">
                <h2>Global gaming stats</h2>
                <div class="Most popular">
                    Display players who owns all games of the selected type.
                    <?php
                    $conn = OpenCon();
                    $query = "SELECT DISTINCT gameType FROM Game ";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        echo "<select name=GType>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['gameType'] . "'>" . $row['gameType'] . "</option>";
                        }
                        echo "</select>";
                    }
                    ?>
                    <input class="Button" type="submit" value="Search" name="dSearch"/> <br/><br/>
                    <?php
                    $conn = OpenCon();
                    if (isset($_POST['dSearch'])) {
                        $GType = $_POST['GType'];
                        $query = "SELECT P.username 
                                    FROM Player P
                                    WHERE NOT EXISTS (
                                       (SELECT G1.gameID FROM Game G1 WHERE G1.gameType = '$GType')
                                       EXCEPT
                                       (SELECT H.gameID 
                                        FROM HasPlayer_Game_Accomplishment H, Game G2 
                                        WHERE H.playerID = P.playerID)
                                    )";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            echo $_POST['GType'];
                            echo "<table border=1>";
                            echo "<tr><th>Player Name</th></tr>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr> <td>" . $row["username"] . "</td></tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "No records";
                        }
                    }
                    ?>
                </div>
                <br/><br/>
                <div class="MaxLength">
                    Max gaming length of
                    <?php
                    $conn = OpenCon();
                    $query = "SELECT gameName FROM Game ";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        echo "<select name=lGameName>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['gameName'] . "'>" . $row['gameName'] . "</option>";
                        }
                        echo "</select>";
                    }
                    ?>
                    in each Region
                    <input class="Button" type="submit" value="Search" name="lSearch"/> <br/><br/>
                    <?php
                    $conn = OpenCon();
                    if (isset($_POST['lSearch'])) {
                        $GameName = $_POST['lGameName'];
                        $query = "SELECT MAX(H.timeSpent), P.location 
                                    FROM HasPlayer_Game_Accomplishment H, Player P, Game G 
                                    WHERE H.playerID = P.playerID AND H.gameID = G.gameID AND G.gameName = '$GameName' 
                                    GROUP BY P.location";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            echo $_POST['lGameName'];
                            echo "<table border=1>";
                            echo "<tr><th>Location</th><th>Max time spent (hours)</th></tr>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr> <td>" . $row["location"] . "</td> <td>" . $row["MAX(H.timeSpent)"] . "</td> </tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "No records";
                        }
                    }
                    ?>
                </div>
                <br/><br/>

                <div class="PlayerNum">
                    Player numbers of
                    <?php
                    $conn = OpenCon();
                    $query = "SELECT gameName FROM Game ";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        echo "<select name=nGameName>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['gameName'] . "'>" . $row['gameName'] . "</option>";
                        }
                        echo "</select>";
                    } ?> in each Region (min gaming length
                    <input type="text" name="length" value=0> hours)
                    <input class="Button" type="submit" value="Search" name="nSearch"/> <br/><br/>
                    <?php
                    $conn = OpenCon();
                    if (isset($_POST['length']) and isset($_POST['nSearch'])) {
                        $GameName = $_POST['nGameName'];
                        $length = $_POST['length'];
                        $query = "SELECT COUNT(DISTINCT P.playerID), P.location 
                                    FROM HasPlayer_Game_Accomplishment H, Player P, Game G 
                                    WHERE H.playerID = P.playerID AND H.gameID = G.gameID AND G.gameName = '$GameName' 
                                    GROUP BY P.location 
                                    HAVING MIN(H.timeSpent)>='$length'";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            echo $_POST['nGameName'] . "---";
                            echo "min gaming length " . $_POST['length'] . " hours";
                            echo "<table border=1>";
                            echo "<tr><th>Location</th><th>Num of players</th></tr>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr> <td>" . $row["location"] . "</td> <td>" . $row["COUNT(DISTINCT P.playerID)"] . "</td> </tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "No records";
                        }
                    }
                    ?>
                </div>
                <br/><br/>

                <div class="MaxAvg">
                    Games that has max avg gaming length of type
                    <?php
                    $conn = OpenCon();
                    $query = "SELECT DISTINCT gameType FROM Game ";
                    $result = $conn->query($query);
                    if ($result->num_rows > 0) {
                        echo "<select name=GameType>";
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['gameType'] . "'>" . $row['gameType'] . "</option>";
                        }
                        echo "</select>";
                    }
                    ?>
                    <input class="Button" type="submit" value="Search" name="aSearch"/> <br/><br/>
                    <?php
                    $conn = OpenCon();
                    if (isset($_POST['aSearch'])) {
                        $GameType = $_POST['GameType'];
                        $query = "SELECT G.gameName, AVG(H.timeSpent)  
                                    FROM HasPlayer_Game_Accomplishment H, Game G 
                                    WHERE H.gameID = G.gameID AND G.gameType = '$GameType' 
                                    GROUP BY G.gameName 
                                    HAVING AVG(H.timeSpent) >= ALL(SELECT AVG(H1.timeSpent) 
                                                                    FROM HasPlayer_Game_Accomplishment H1, Game G1 
                                                                    WHERE H1.gameID = G1.gameID AND G1.gameType = '$GameType' 
                                                                    GROUP BY G1.gameName)";
                        $result = $conn->query($query);
                        if ($result->num_rows > 0) {
                            echo $_POST['GameType'];
                            echo "<table border=1>";
                            echo "<tr><th>Location</th><th>Max time spent (hours)</th></tr>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr> <td>" . $row["gameName"] . "</td> <td>" . $row["AVG(H.timeSpent)"] . "</td> </tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "No records";
                        }
                    }
                    ?>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>