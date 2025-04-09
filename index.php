<?php

function consultarCEP($cep) {
 
    $cep = preg_replace('/[^0-9]/', '', $cep);
    
 
    if (strlen($cep) !== 8) {
        return [
            'error' => true,
            'message' => 'CEP inválido. Digite 8 números.'
        ];
    }
    
  
    $url = "https://viacep.com.br/ws/{$cep}/json/";
    
    try {
        $response = file_get_contents($url);
        $data = json_decode($response, true);
        
        
        if (isset($data['erro']) && $data['erro'] === true) {
            return [
                'error' => true,
                'message' => 'CEP não encontrado. Verifique e tente novamente.'
            ];
        }
        
        return [
            'error' => false,
            'data' => $data
        ];
    } catch (Exception $e) {
        return [
            'error' => true,
            'message' => 'Erro ao consultar o CEP. Tente novamente.'
        ];
    }
}


$resultado = null;
$mensagemErro = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cep'])) {
    $cep = $_POST['cep'];
    $resposta = consultarCEP($cep);
    
    if ($resposta['error']) {
        $mensagemErro = $resposta['message'];
    } else {
        $resultado = $resposta['data'];
    }
}


function formatarCEP($cep) {
    return substr($cep, 0, 5) . '-' . substr($cep, 5);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de CEP</title>
    <style>
        :root {
            --primary: #4A56E2;
            --secondary: #6C63FF;
            --success: #28a745;
            --error: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .container:hover {
            transform: translateY(-5px);
        }
        
        header {
            background-color: var(--primary);
            color: white;
            padding: 25px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }
        
        header p {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .header-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            z-index: 0;
        }
        
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 15px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z' fill='%23ffffff' opacity='0.25'%3E%3C/path%3E%3Cpath d='M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z' fill='%23ffffff' opacity='0.5'%3E%3C/path%3E%3Cpath d='M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z' fill='%23ffffff' opacity='0.75'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
        }
        
        .search-area {
            padding: 30px;
            text-align: center;
        }
        
        .input-group {
            display: flex;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            border-radius: 50px;
            overflow: hidden;
        }
        
        #cep-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid #e4e8f0;
            border-right: none;
            border-top-left-radius: 50px;
            border-bottom-left-radius: 50px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }
        
        #cep-input:focus {
            border-color: var(--primary);
        }
        
        #search-btn {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            cursor: pointer;
            border-top-right-radius: 50px;
            border-bottom-right-radius: 50px;
            font-size: 1rem;
            font-weight: bold;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #search-btn:hover {
            background-color: var(--secondary);
        }
        
        #search-btn i {
            margin-right: 8px;
        }
        
        .result-area {
            padding: 0 30px 30px;
            <?php if (!$resultado): ?>display: none;<?php endif; ?>
        }
        
        .result-card {
            background-color: var(--light);
            border-radius: 12px;
            padding: 20px;
            animation: fadeIn 0.5s ease;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .result-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }
        
        .result-item {
            flex: 1 0 calc(50% - 20px);
            margin: 10px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
            animation: slideUp 0.3s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        
        .result-item h3 {
            color: var(--primary);
            margin-bottom: 5px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .result-item p {
            color: var(--dark);
            font-size: 1.1rem;
        }
        
        .loader {
            display: none;
            width: 48px;
            height: 48px;
            border: 5px solid var(--light);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        .error-message {
            background-color: var(--error);
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            <?php if (!$mensagemErro): ?>display: none;<?php endif; ?>
            animation: shake 0.5s ease;
            text-align: center;
        }
        
        .map-preview {
            margin-top: 20px;
            border-radius: 12px;
            overflow: hidden;
            height: 200px;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            animation: fadeIn 0.5s ease;
        }
        
        footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Animações */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes slideUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        /* Media Queries para Responsividade */
        @media (max-width: 768px) {
            .container {
                width: 95%;
            }
            
            header h1 {
                font-size: 1.5rem;
            }
            
            .result-item {
                flex: 1 0 100%;
            }
        }
        
        @media (max-width: 480px) {
            .input-group {
                flex-direction: column;
                border-radius: 10px;
                box-shadow: none;
            }
            
            #cep-input {
                border: 2px solid #e4e8f0;
                border-radius: 10px;
                margin-bottom: 10px;
            }
            
            #search-btn {
                border-radius: 10px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-animation"></div>
            <div class="wave"></div>
            <h1>Consulta de CEP</h1>
            <p>Encontre facilmente endereços através do CEP</p>
        </header>
        
        <div class="search-area">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" id="cep-form">
                <div class="input-group">
                    <input type="text" id="cep-input" name="cep" placeholder="Digite o CEP (somente números)" maxlength="8" value="<?php echo isset($_POST['cep']) ? htmlspecialchars($_POST['cep']) : ''; ?>" required>
                    <button type="submit" id="search-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 8px;">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Consultar
                    </button>
                </div>
            </form>
            
            <div class="loader" id="loader"></div>
            <div class="error-message" id="error-message">
                <?php if ($mensagemErro): echo $mensagemErro; endif; ?>
            </div>
        </div>
        
        <div class="result-area" id="result-area">
            <?php if ($resultado): ?>
            <div class="result-card">
                <div class="result-row" id="result-content">
                    <div class="result-item" style="animation-delay: 0ms; opacity: 1; transform: translateY(0);">
                        <h3>CEP</h3>
                        <p><?php echo formatarCEP($resultado['cep']); ?></p>
                    </div>
                    <div class="result-item" style="animation-delay: 100ms; opacity: 1; transform: translateY(0);">
                        <h3>Logradouro</h3>
                        <p><?php echo $resultado['logradouro'] ?: 'Não informado'; ?></p>
                    </div>
                    <div class="result-item" style="animation-delay: 200ms; opacity: 1; transform: translateY(0);">
                        <h3>Complemento</h3>
                        <p><?php echo $resultado['complemento'] ?: 'Não informado'; ?></p>
                    </div>
                    <div class="result-item" style="animation-delay: 300ms; opacity: 1; transform: translateY(0);">
                        <h3>Bairro</h3>
                        <p><?php echo $resultado['bairro'] ?: 'Não informado'; ?></p>
                    </div>
                    <div class="result-item" style="animation-delay: 400ms; opacity: 1; transform: translateY(0);">
                        <h3>Cidade</h3>
                        <p><?php echo $resultado['localidade'] ?: 'Não informado'; ?></p>
                    </div>
                    <div class="result-item" style="animation-delay: 500ms; opacity: 1; transform: translateY(0);">
                        <h3>Estado</h3>
                        <p><?php echo $resultado['uf'] ?: 'Não informado'; ?></p>
                    </div>
                </div>
                <div class="map-preview pulse" id="map-preview">
                    <div style="text-align: center;">
                        <div style="font-size: 3rem; margin-bottom: 10px;">📍</div>
                        <div style="font-weight: bold;"><?php echo $resultado['logradouro'] ?: ''; ?></div>
                        <div><?php echo $resultado['bairro'] . ' - ' . $resultado['localidade'] . '/' . $resultado['uf']; ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <p>By João Marçal, Hamilton Tonelotti e Jeferson Juiz</p>
    </footer>

    <script>
        // Elementos do DOM
        const cepInput = document.getElementById('cep-input');
        const cepForm = document.getElementById('cep-form');
        const loader = document.getElementById('loader');
        
        // Efeito de animação no cabeçalho
        const headerAnimation = document.querySelector('.header-animation');
        
        // Função para lidar com a animação do cabeçalho
        function animateHeader() {
            const colors = [
                'linear-gradient(45deg, #4A56E2, #6C63FF)',
                'linear-gradient(45deg, #5465FF, #788BFF)',
                'linear-gradient(45deg, #4361EE, #4CC9F0)'
            ];
            
            let colorIndex = 0;
            
            setInterval(() => {
                colorIndex = (colorIndex + 1) % colors.length;
                headerAnimation.style.background = colors[colorIndex];
            }, 3000);
        }
        
        // Iniciar animação do cabeçalho
        animateHeader();
        
        // Função para validar entrada apenas de números
        cepInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
        
        // Mostrar o loader ao enviar o formulário
        cepForm.addEventListener('submit', function() {
            loader.style.display = 'block';
        });
        
        // Aplicar efeito ao carregar a página
        document.querySelector('.container').classList.add('pulse');
        setTimeout(() => {
            document.querySelector('.container').classList.remove('pulse');
        }, 2000);
        
        <?php if ($resultado): ?>
        // Animar os itens de resultado se houver resultado
        document.querySelectorAll('.result-item').forEach((item, index) => {
            setTimeout(() => {
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 100);
        });
        <?php endif; ?>
    </script>
</body>
</html>