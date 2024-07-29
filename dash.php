<?php
require 'config.php'; // Inclui a configuração da sessão
require 'funcs.php';
require_once 'db.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../index.php');
    exit;
}

// Função para obter dados das vendas
function getSalesData($db) {
    $sql = "SELECT SUM(Valor) as total_sales, COUNT(DISTINCT Cliente_fk) as total_customers FROM vendas";
    $result = $db->send2db($sql);

    if ($result === false) {
        die('Erro ao executar a consulta: ' . $db->error);
    }

    return $result->fetch_assoc();
}

// Função para obter dados do gráfico de vendas (mensalmente)
function getSalesChartData($db) {
    $sql = "SELECT DATE_FORMAT(dataVenda, '%Y-%m') as month, SUM(Valor) as total_sales FROM vendas GROUP BY month ORDER BY month";
    $result = $db->send2db($sql);

    if ($result === false) {
        die('Erro ao executar a consulta: ' . $db->error);
    }

    $sales_data = [];
    while ($row = $result->fetch_assoc()) {
        $sales_data[] = $row;
    }

    return $sales_data;
}

$db = new DB(); 
$sales_data = getSalesData($db);
$sales_chart_data = getSalesChartData($db);
?>

<!DOCTYPE html>
<html lang="pt-pt">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo_icon.png">
    <link rel="stylesheet" href="/css/styledas.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <title>DasPro - Admin Panel</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
               <img src="../img/logo_icon.png" alt="">
            </div>
            <span class="logo_name">DashPro</span>
        </div>

        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="dash.php">
                    <i class="uil uil-estate"></i>
                    <span class="link-name">Home</span>
                </a></li>
                <li><a href="Clients.php">
                    <i class="uil uil-users-alt"></i>
                    <span class="link-name">Clients</span>
                </a></li>
                <li><a href="Produts.php">
                    <i class="uil uil-shopping-cart-alt"></i>
                    <span class="link-name">Products</span>
                </a></li>
                <li><a href="Vendas.php">
                    <i class="uil uil-comments"></i>
                    <span class="link-name">Sells</span>
                </a></li>
                <li><a href="Profile.php" id ="USER_link">
                    <i class="uil uil-user"></i>
                    <span class="link-name">Profile</span>
                </a></li>
            </ul>
            
            <ul class="logout-mode">
                <li><a href="logout.php">
                    <i class="uil uil-signout"></i>
                    <span class="link-name">Logout</span>
                </a></li>

                <li class="mode">
                    <a href="#">
                        <i class="uil uil-moon"></i>
                    <span class="link-name">Dark Mode</span>
                </a>

                <div class="mode-toggle">
                  <span class="switch"></span>
                </div>
            </li>
            </ul>
        </div>
    </nav>

    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>
        </div>

        <div class="container">
            <div class="card-gr">
                <div class="card-gr-header"></div>
                <div class="card-gr-body">
                    <div class="totals">
                        <div class="total-item">
                            <h5>Total Sales</h5>
                            <p><?php echo htmlspecialchars($sales_data['total_sales']); ?> €</p>
                        </div>
                        <div class="total-item">
                            <h5>Total Customers</h5>
                            <p><?php echo htmlspecialchars($sales_data['total_customers']); ?></p>
                        </div>
                    </div>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Dados do gráfico de vendas
        const salesChartData = <?php echo json_encode($sales_chart_data); ?>;

        // Formatação dos dados para o gráfico
        const labels = salesChartData.map(data => data.month);
        const data = salesChartData.map(data => data.total_sales);

        // Configuração do gráfico
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Vendas Mensais (€)',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(75, 192, 192, 1)',
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(200, 200, 200, 0.2)',
                        },
                        ticks: {
                            color: '#555',
                            callback: function(value) {
                                return '€ ' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(200, 200, 200, 0.2)',
                        },
                        ticks: {
                            color: '#555',
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(75, 75, 75, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        footerColor: '#fff',
                        callbacks: {
                            label: function(tooltipItem) {
                                return '€ ' + tooltipItem.raw;
                            }
                        }
                    },
                    legend: {
                        labels: {
                            color: '#555',
                        }
                    }
                }
            }
        });
    </script>
    <script src="../js/script.js"></script>
    <script src="../js/click.js"></script>
</body>
</html>
