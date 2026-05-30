<?php
// 1. BANCO DE DADOS DE RECEITAS ANTI-DESPERDÍCIO
$receitas = [
    [
        "nome" => "Omelete Completo de Geladeira",
        "ingredientes" => ["ovo", "tomate", "cebola", "queijo", "presunto", "sal"],
        "preparo" => "Bata os ovos vigorosamente, pique todos os frios e vegetais que tiver, misture tudo e doure na frigideira untada com manteiga ou óleo dos dois lados."
    ],
    [
        "nome" => "Frittata de Legumes Reutilizados",
        "ingredientes" => ["ovo", "batata", "cenoura", "abobrinha", "cebola", "queijo"],
        "preparo" => "Corte os legumes em cubos pequenos e refogue na frigideira. Bata os ovos com sal e jogue por cima dos legumes. Deixe cozinhar em fogo baixo com a tampa fechada até firmar."
    ],
    [
        "nome" => "Ovos no Purgatório (Shakshuka)",
        "ingredientes" => ["ovo", "tomate", "extrato de tomate", "cebola", "alho", "pao"],
        "preparo" => "Faça um molho de tomate bem encorpado e temperado na frigideira. Abra buracos no molho, quebre os ovos diretamente neles, tampe e deixe cozinhar. Coma chuchando o pão."
    ],
    [
        "nome" => "Bruschetta de Pão Amanhecido",
        "ingredientes" => ["pao", "tomate", "alho", "azeite", "queijo"],
        "preparo" => "Corte o pão amanhecido em fatias. Esfregue um dente de alho cru nelas, regue com azeite, cubra com tomates picados temperados e queijo, e leve ao forno até dourar."
    ],
    [
        "nome" => "Rabanada de Forno Fácil",
        "ingredientes" => ["pao", "ovo", "leite", "acucar", "canela"],
        "preparo" => "Passe as fatias de pão no leite doce e depois no ovo batido. Arrume em uma forma untada e asse até dourar. Passe no açúcar com canela."
    ],
    [
        "nome" => "Bolinho de Arroz de Ontem",
        "ingredientes" => ["arroz", "ovo", "farinha de trigo", "queijo", "cebola", "leite"],
        "preparo" => "Misture o arroz de ontem com o ovo, um pouco de leite, queijo ralado e farinha até dar liga de moldar. Faça bolinhas e frite em óleo quente até dourar."
    ],
    [
        "nome" => "Arroz de Forno Cremoso",
        "ingredientes" => ["arroz", "creme de leite", "queijo", "presunto", "tomate", "frango"],
        "preparo" => "Misture o arroz cozido com o creme de leite, o frango desfiado (ou presunto) e os tomates. Cubra com bastante queijo e leve ao forno para gratinar."
    ],
    [
        "nome" => "Carreteiro de Sobras de Churrasco",
        "ingredientes" => ["carne", "calabresa", "arroz", "cebola", "alho", "tomate"],
        "preparo" => "Pique bem as sobras de carne assada ou churrasco. Refogue com cebola, alho, tomate e calabresa. Adicione o arroz, cubra com água fervente e cozinhe até secar."
    ],
    [
        "nome" => "Frango Desfiado Gratinado",
        "ingredientes" => ["frango", "requeijao", "milho", "queijo", "cebola"],
        "preparo" => "Refogue o frango desfiado com cebola e milho. Misture o requeijão, coloque tudo em um refratário, cubra com queijo muçarela e leve ao forno até dourar a superfície."
    ],
    [
        "nome" => "Frittata de Macarrão Amanhecido",
        "ingredientes" => ["macarrao", "ovo", "queijo", "tomate", "cebola"],
        "preparo" => "Bata os ovos temperados com sal. Misture o macarrão que sobrou no prato de ovos. Despeje em uma frigideira com óleo e frite como um omelete grosso dos dois lados."
    ],
    [
        "nome" => "Sopa Quente de Legumes",
        "ingredientes" => ["batata", "cenoura", "abobrinha", "cebola", "alho", "macarrao"],
        "preparo" => "Pique todos os legumes moles ou esquecidos na gaveta. Refogue cebola e alho, jogue os legumes, cubra com água e cozinhe. Adicione um punhado de macarrão quebrado no final."
    ],
    [
        "nome" => "Doce de Banana de Panela",
        "ingredientes" => ["banana", "acucar", "agua", "canela"],
        "preparo" => "Ideal para usar bananas maduras demais. Derreta o açúcar na panela até virar caramelo claro, jogue a água cuidadosamente e depois coloque as bananas fatiadas com canela. Cozinhe até amaciar."
    ]
];

// 2. HIGIENIZAÇÃO DE TEXTO
if (!function_exists('limparTexto')) {
    function limparTexto($texto) {
        $texto = trim(mb_strtolower($texto, 'UTF-8'));
        $mapa = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c'];
        return strtr($texto, $mapa);
    }
}

$receitas_sugeridas = [];
$ingredientes_usuario = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ingredientes'])) {
    $input_bruto = $_POST['ingredientes'];
    $partes = explode(',', $input_bruto);
    
    foreach ($partes as $parte) {
        $limpo = limparTexto($parte);
        if (!empty($limpo)) {
            $ingredientes_usuario[] = $limpo;
        }
    }

    foreach ($receitas as $receita) {
        $match_count = 0;
        $comuns = [];
        $faltando = [];

        foreach ($receita['ingredientes'] as $ing_receita) {
            $achou = false;
            foreach ($ingredientes_usuario as $ing_user) {
                if (strpos($ing_receita, $ing_user) !== false || strpos($ing_user, $ing_receita) !== false) {
                    $achou = true;
                    break;
                }
            }

            if ($achou) {
                $match_count++;
                $comuns[] = $ing_receita;
            } else {
                $faltando[] = $ing_receita;
            }
        }

        if ($match_count > 0) {
            $receita['match_count'] = $match_count;
            $receita['ingredientes_comuns'] = $comuns;
            $receita['ingredientes_faltando'] = $faltando;
            $receita['aproveitamento'] = round(($match_count / count($receita['ingredientes'])) * 100);
            $receitas_sugeridas[] = $receita;
        }
    }

    usort($receitas_sugeridas, function($a, $b) {
        return $b['aproveitamento'] <=> $a['aproveitamento'];
    });
}

// Coleta ingredientes para a lista clicável
$todos_ingredientes_disponiveis = [];
foreach ($receitas as $r) { $todos_ingredientes_disponiveis = array_merge($todos_ingredientes_disponiveis, $r['ingredientes']); }
$todos_ingredientes_disponiveis = array_unique($todos_ingredientes_disponiveis);
sort($todos_ingredientes_disponiveis);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefe de Geladeira | Antidesperdício</title>
    <!-- FontAwesome para ícones bonitos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --accent-green: #d1fae5;
            --accent-green-text: #065f46;
            --accent-red: #fee2e2;
            --accent-red-text: #991b1b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background-color: var(--background); color: var(--text-main); padding: 40px 20px; line-height: 1.5; }

        .app-container { max-width: 900px; margin: 0 auto; }

        /* Header UI */
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 2.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -1px; margin-bottom: 8px; }
        .header h1 span { color: var(--primary); }
        .header p { color: var(--text-muted); font-size: 1.1rem; }

        /* Card Principal de Entrada */
        .search-card { background: var(--card-bg); border-radius: 20px; padding: 35px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; margin-bottom: 40px; }
        .input-wrapper { position: relative; display: flex; align-items: center; margin-bottom: 20px; }
        .input-wrapper i { position: absolute; left: 20px; color: var(--text-muted); font-size: 1.2rem; }
        
        .campo-busca { 
            width: 100%; 
            padding: 18px 20px 18px 55px; 
            font-size: 1rem; 
            border-radius: 14px; 
            border: 2px solid #e2e8f0; 
            background: #f8fafc;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .campo-busca:focus { border-color: var(--primary); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

        /* Tags rápidas */
        .tag-title-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .tag-title-area h4 { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
        .btn-clear { background: none; border: none; color: #ef4444; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: opacity 0.2s; }
        .btn-clear:hover { opacity: 0.8; }
        
        .tags-container { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 25px; max-height: 120px; overflow-y: auto; padding-right: 5px; }
        /* Scrollbar customizado para as tags */
        .tags-container::-webkit-scrollbar { width: 4px; }
        .tags-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .tag-item { 
            background: #f1f5f9; 
            padding: 8px 14px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: #475569;
            cursor: pointer; 
            transition:  all 0.2s ease;
            border: 1px solid #e2e8f0;
        }
        .tag-item:hover { background: var(--accent-green); color: var(--accent-green-text); border-color: var(--primary); transform: translateY(-1px); }

        .btn-submit { 
            background: var(--primary); 
            color: white; 
            padding: 16px; 
            border: none; 
            border-radius: 14px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 1.1rem; 
            font-weight: 700; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3); }

        /* Grid de Resultados */
        .results-section h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .recipe-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        
        @media(min-width: 768px) {
            .recipe-grid { grid-template-columns: 1fr 1fr; }
        }

        /* Card de Receita Estilizado */
        .recipe-card { 
            background: var(--card-bg); 
            border-radius: 18px; 
            padding: 25px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.006);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .recipe-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }

        .recipe-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; gap: 10px; }
        .recipe-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); line-height: 1.3; }
        
        .match-badge { 
            background: var(--accent-green); 
            color: var(--accent-green-text); 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            white-space: nowrap;
        }

        .ingredients-block { margin-bottom: 15px; }
        .block-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px; }
        
        .badge-list { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
        .ing-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 600; }
        .ing-badge.have { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .ing-badge.missing { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

        .preparo-text { 
            font-size: 0.9rem; 
            color: #475569; 
            background: #f8fafc; 
            padding: 15px; 
            border-radius: 12px; 
            border: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .empty-state { text-align: center; padding: 40px; background: #fff; border-radius: 20px; border: 2px dashed #cbd5e1; color: var(--text-muted); }
        .empty-state i { font-size: 2.5rem; margin-bottom: 15px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- Cabeçalho -->
    <header class="header">
        <h1>Chef da <span>Geladeira</span></h1>
        <p>Economize dinheiro e evite o desperdício com o que você já tem em casa.</p>
    </header>

    <!-- Painel de Busca -->
    <section class="search-card">
        <form method="POST" id="form-receitas">
            <div class="input-wrapper">
                <i class="fa-solid :fa-basket-shopping fa-utensils"></i>
                <input type="text" name="ingredientes" id="input-ingredientes" class="campo-busca" 
                       placeholder="Ex: ovo, tomate, pao, arroz..." 
                       value="<?php echo isset($_POST['ingredientes']) ? htmlspecialchars($_POST['ingredientes']) : ''; ?>" required>
            </div>
            
            <div class="tag-title-area">
                <h4><i class="fa-solid fa-hand-pointer"></i> Toque rápido para selecionar:</h4>
                <button type="button" class="btn-clear" onclick="limparInput()"><i class="fa-solid fa-trash-can"></i> Limpar tudo</button>
            </div>

            <div class="tags-container">
                <?php foreach ($todos_ingredientes_disponiveis as $ing_disponivel): ?>
                    <span class="tag-item" onclick="adicionarIngrediente('<?php echo $ing_disponivel; ?>')">+ <?php echo $ing_disponivel; ?></span>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Combinar Ingredientes
            </button>
        </form>
    </section>

    <!-- Seção de Resultados -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <section class="results-section">
            <h2><i class="fa-solid fa-kitchen-set"></i> Receitas Sugeridas</h2>
            
            <?php if (empty($receitas_sugeridas)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-triangle-excursion"></i>
                    <p>Nenhuma combinação direta foi encontrada para esses itens.</p>
                    <p style="font-size: 0.85rem; margin-top: 5px;">Dica: Tente palavras básicas como 'ovo', 'pao' ou 'arroz'.</p>
                </div>
            <?php else: ?>
                <div class="recipe-grid">
                    <?php foreach ($receitas_sugeridas as $rec): ?>
                        <div class="recipe-card">
                            <div>
                                <div class="recipe-header">
                                    <h3 class="recipe-title"><?php echo $rec['nome']; ?></h3>
                                    <span class="match-badge"><?php echo $rec['aproveitamento']; ?>% Match</span>
                                </div>
                                
                                <div class="ingredients-block">
                                    <div class="block-title">Você já tem:</div>
                                    <div class="badge-list">
                                        <?php foreach($rec['ingredientes_comuns'] as $ing): ?>
                                            <span class="ing-badge have"><i class="fa-solid fa-check"></i> <?php echo $ing; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (!empty($rec['ingredientes_faltando'])): ?>
                                        <div class="block-title">Falta na sua despensa:</div>
                                        <div class="badge-list">
                                            <?php foreach($rec['ingredientes_faltando'] as $ing): ?>
                                                <span class="ing-badge missing"><i class="fa-solid fa-plus"></i> <?php echo $ing; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="preparo-text">
                                <strong><i class="fa-solid fa-fire-burner"></i> Como fazer:</strong><br>
                                <?php echo $rec['preparo']; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>

<script>
function adicionarIngrediente(nome) {
    var input = document.getElementById('input-ingredientes');
    var valorAtual = input.value.trim();
    
    if (valorAtual === "") {
        input.value = nome;
    } else {
        var itens = valorAtual.split(',').map(item => item.trim());
        if (!itens.includes(nome)) {
            input.value = valorAtual + ", " + nome;
        }
    }
}

function limparInput() {
    document.getElementById('input-ingredientes').value = "";
}
</script>

</body>
</html><?php
// 1. BANCO DE DADOS DE RECEITAS ANTI-DESPERDÍCIO
$receitas = [
    [
        "nome" => "Omelete Completo de Geladeira",
        "ingredientes" => ["ovo", "tomate", "cebola", "queijo", "presunto", "sal"],
        "preparo" => "Bata os ovos vigorosamente, pique todos os frios e vegetais que tiver, misture tudo e doure na frigideira untada com manteiga ou óleo dos dois lados."
    ],
    [
        "nome" => "Frittata de Legumes Reutilizados",
        "ingredientes" => ["ovo", "batata", "cenoura", "abobrinha", "cebola", "queijo"],
        "preparo" => "Corte os legumes em cubos pequenos e refogue na frigideira. Bata os ovos com sal e jogue por cima dos legumes. Deixe cozinhar em fogo baixo com a tampa fechada até firmar."
    ],
    [
        "nome" => "Ovos no Purgatório (Shakshuka)",
        "ingredientes" => ["ovo", "tomate", "extrato de tomate", "cebola", "alho", "pao"],
        "preparo" => "Faça um molho de tomate bem encorpado e temperado na frigideira. Abra buracos no molho, quebre os ovos diretamente neles, tampe e deixe cozinhar. Coma chuchando o pão."
    ],
    [
        "nome" => "Bruschetta de Pão Amanhecido",
        "ingredientes" => ["pao", "tomate", "alho", "azeite", "queijo"],
        "preparo" => "Corte o pão amanhecido em fatias. Esfregue um dente de alho cru nelas, regue com azeite, cubra com tomates picados temperados e queijo, e leve ao forno até dourar."
    ],
    [
        "nome" => "Rabanada de Forno Fácil",
        "ingredientes" => ["pao", "ovo", "leite", "acucar", "canela"],
        "preparo" => "Passe as fatias de pão no leite doce e depois no ovo batido. Arrume em uma forma untada e asse até dourar. Passe no açúcar com canela."
    ],
    [
        "nome" => "Bolinho de Arroz de Ontem",
        "ingredientes" => ["arroz", "ovo", "farinha de trigo", "queijo", "cebola", "leite"],
        "preparo" => "Misture o arroz de ontem com o ovo, um pouco de leite, queijo ralado e farinha até dar liga de moldar. Faça bolinhas e frite em óleo quente até dourar."
    ],
    [
        "nome" => "Arroz de Forno Cremoso",
        "ingredientes" => ["arroz", "creme de leite", "queijo", "presunto", "tomate", "frango"],
        "preparo" => "Misture o arroz cozido com o creme de leite, o frango desfiado (ou presunto) e os tomates. Cubra com bastante queijo e leve ao forno para gratinar."
    ],
    [
        "nome" => "Carreteiro de Sobras de Churrasco",
        "ingredientes" => ["carne", "calabresa", "arroz", "cebola", "alho", "tomate"],
        "preparo" => "Pique bem as sobras de carne assada ou churrasco. Refogue com cebola, alho, tomate e calabresa. Adicione o arroz, cubra com água fervente e cozinhe até secar."
    ],
    [
        "nome" => "Frango Desfiado Gratinado",
        "ingredientes" => ["frango", "requeijao", "milho", "queijo", "cebola"],
        "preparo" => "Refogue o frango desfiado com cebola e milho. Misture o requeijão, coloque tudo em um refratário, cubra com queijo muçarela e leve ao forno até dourar a superfície."
    ],
    [
        "nome" => "Frittata de Macarrão Amanhecido",
        "ingredientes" => ["macarrao", "ovo", "queijo", "tomate", "cebola"],
        "preparo" => "Bata os ovos temperados com sal. Misture o macarrão que sobrou no prato de ovos. Despeje em uma frigideira com óleo e frite como um omelete grosso dos dois lados."
    ],
    [
        "nome" => "Sopa Quente de Legumes",
        "ingredientes" => ["batata", "cenoura", "abobrinha", "cebola", "alho", "macarrao"],
        "preparo" => "Pique todos os legumes moles ou esquecidos na gaveta. Refogue cebola e alho, jogue os legumes, cubra com água e cozinhe. Adicione um punhado de macarrão quebrado no final."
    ],
    [
        "nome" => "Doce de Banana de Panela",
        "ingredientes" => ["banana", "acucar", "agua", "canela"],
        "preparo" => "Ideal para usar bananas maduras demais. Derreta o açúcar na panela até virar caramelo claro, jogue a água cuidadosamente e depois coloque as bananas fatiadas com canela. Cozinhe até amaciar."
    ]
];

// 2. HIGIENIZAÇÃO DE TEXTO
if (!function_exists('limparTexto')) {
    function limparTexto($texto) {
        $texto = trim(mb_strtolower($texto, 'UTF-8'));
        $mapa = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c'];
        return strtr($texto, $mapa);
    }
}

$receitas_sugeridas = [];
$ingredientes_usuario = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ingredientes'])) {
    $input_bruto = $_POST['ingredientes'];
    $partes = explode(',', $input_bruto);
    
    foreach ($partes as $parte) {
        $limpo = limparTexto($parte);
        if (!empty($limpo)) {
            $ingredientes_usuario[] = $limpo;
        }
    }

    foreach ($receitas as $receita) {
        $match_count = 0;
        $comuns = [];
        $faltando = [];

        foreach ($receita['ingredientes'] as $ing_receita) {
            $achou = false;
            foreach ($ingredientes_usuario as $ing_user) {
                if (strpos($ing_receita, $ing_user) !== false || strpos($ing_user, $ing_receita) !== false) {
                    $achou = true;
                    break;
                }
            }

            if ($achou) {
                $match_count++;
                $comuns[] = $ing_receita;
            } else {
                $faltando[] = $ing_receita;
            }
        }

        if ($match_count > 0) {
            $receita['match_count'] = $match_count;
            $receita['ingredientes_comuns'] = $comuns;
            $receita['ingredientes_faltando'] = $faltando;
            $receita['aproveitamento'] = round(($match_count / count($receita['ingredientes'])) * 100);
            $receitas_sugeridas[] = $receita;
        }
    }

    usort($receitas_sugeridas, function($a, $b) {
        return $b['aproveitamento'] <=> $a['aproveitamento'];
    });
}

// Coleta ingredientes para a lista clicável
$todos_ingredientes_disponiveis = [];
foreach ($receitas as $r) { $todos_ingredientes_disponiveis = array_merge($todos_ingredientes_disponiveis, $r['ingredientes']); }
$todos_ingredientes_disponiveis = array_unique($todos_ingredientes_disponiveis);
sort($todos_ingredientes_disponiveis);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefe de Geladeira | Antidesperdício</title>
    <!-- FontAwesome para ícones bonitos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --accent-green: #d1fae5;
            --accent-green-text: #065f46;
            --accent-red: #fee2e2;
            --accent-red-text: #991b1b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background-color: var(--background); color: var(--text-main); padding: 40px 20px; line-height: 1.5; }

        .app-container { max-width: 900px; margin: 0 auto; }

        /* Header UI */
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 2.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -1px; margin-bottom: 8px; }
        .header h1 span { color: var(--primary); }
        .header p { color: var(--text-muted); font-size: 1.1rem; }

        /* Card Principal de Entrada */
        .search-card { background: var(--card-bg); border-radius: 20px; padding: 35px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; margin-bottom: 40px; }
        .input-wrapper { position: relative; display: flex; align-items: center; margin-bottom: 20px; }
        .input-wrapper i { position: absolute; left: 20px; color: var(--text-muted); font-size: 1.2rem; }
        
        .campo-busca { 
            width: 100%; 
            padding: 18px 20px 18px 55px; 
            font-size: 1rem; 
            border-radius: 14px; 
            border: 2px solid #e2e8f0; 
            background: #f8fafc;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .campo-busca:focus { border-color: var(--primary); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

        /* Tags rápidas */
        .tag-title-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .tag-title-area h4 { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
        .btn-clear { background: none; border: none; color: #ef4444; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: opacity 0.2s; }
        .btn-clear:hover { opacity: 0.8; }
        
        .tags-container { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 25px; max-height: 120px; overflow-y: auto; padding-right: 5px; }
        /* Scrollbar customizado para as tags */
        .tags-container::-webkit-scrollbar { width: 4px; }
        .tags-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .tag-item { 
            background: #f1f5f9; 
            padding: 8px 14px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: #475569;
            cursor: pointer; 
            transition:  all 0.2s ease;
            border: 1px solid #e2e8f0;
        }
        .tag-item:hover { background: var(--accent-green); color: var(--accent-green-text); border-color: var(--primary); transform: translateY(-1px); }

        .btn-submit { 
            background: var(--primary); 
            color: white; 
            padding: 16px; 
            border: none; 
            border-radius: 14px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 1.1rem; 
            font-weight: 700; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3); }

        /* Grid de Resultados */
        .results-section h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .recipe-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        
        @media(min-width: 768px) {
            .recipe-grid { grid-template-columns: 1fr 1fr; }
        }

        /* Card de Receita Estilizado */
        .recipe-card { 
            background: var(--card-bg); 
            border-radius: 18px; 
            padding: 25px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.006);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .recipe-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }

        .recipe-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; gap: 10px; }
        .recipe-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); line-height: 1.3; }
        
        .match-badge { 
            background: var(--accent-green); 
            color: var(--accent-green-text); 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            white-space: nowrap;
        }

        .ingredients-block { margin-bottom: 15px; }
        .block-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px; }
        
        .badge-list { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
        .ing-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 600; }
        .ing-badge.have { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .ing-badge.missing { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

        .preparo-text { 
            font-size: 0.9rem; 
            color: #475569; 
            background: #f8fafc; 
            padding: 15px; 
            border-radius: 12px; 
            border: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .empty-state { text-align: center; padding: 40px; background: #fff; border-radius: 20px; border: 2px dashed #cbd5e1; color: var(--text-muted); }
        .empty-state i { font-size: 2.5rem; margin-bottom: 15px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- Cabeçalho -->
    <header class="header">
        <h1>Chef da <span>Geladeira</span></h1>
        <p>Economize dinheiro e evite o desperdício com o que você já tem em casa.</p>
    </header>

    <!-- Painel de Busca -->
    <section class="search-card">
        <form method="POST" id="form-receitas">
            <div class="input-wrapper">
                <i class="fa-solid :fa-basket-shopping fa-utensils"></i>
                <input type="text" name="ingredientes" id="input-ingredientes" class="campo-busca" 
                       placeholder="Ex: ovo, tomate, pao, arroz..." 
                       value="<?php echo isset($_POST['ingredientes']) ? htmlspecialchars($_POST['ingredientes']) : ''; ?>" required>
            </div>
            
            <div class="tag-title-area">
                <h4><i class="fa-solid fa-hand-pointer"></i> Toque rápido para selecionar:</h4>
                <button type="button" class="btn-clear" onclick="limparInput()"><i class="fa-solid fa-trash-can"></i> Limpar tudo</button>
            </div>

            <div class="tags-container">
                <?php foreach ($todos_ingredientes_disponiveis as $ing_disponivel): ?>
                    <span class="tag-item" onclick="adicionarIngrediente('<?php echo $ing_disponivel; ?>')">+ <?php echo $ing_disponivel; ?></span>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Combinar Ingredientes
            </button>
        </form>
    </section>

    <!-- Seção de Resultados -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <section class="results-section">
            <h2><i class="fa-solid fa-kitchen-set"></i> Receitas Sugeridas</h2>
            
            <?php if (empty($receitas_sugeridas)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-triangle-excursion"></i>
                    <p>Nenhuma combinação direta foi encontrada para esses itens.</p>
                    <p style="font-size: 0.85rem; margin-top: 5px;">Dica: Tente palavras básicas como 'ovo', 'pao' ou 'arroz'.</p>
                </div>
            <?php else: ?>
                <div class="recipe-grid">
                    <?php foreach ($receitas_sugeridas as $rec): ?>
                        <div class="recipe-card">
                            <div>
                                <div class="recipe-header">
                                    <h3 class="recipe-title"><?php echo $rec['nome']; ?></h3>
                                    <span class="match-badge"><?php echo $rec['aproveitamento']; ?>% Match</span>
                                </div>
                                
                                <div class="ingredients-block">
                                    <div class="block-title">Você já tem:</div>
                                    <div class="badge-list">
                                        <?php foreach($rec['ingredientes_comuns'] as $ing): ?>
                                            <span class="ing-badge have"><i class="fa-solid fa-check"></i> <?php echo $ing; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (!empty($rec['ingredientes_faltando'])): ?>
                                        <div class="block-title">Falta na sua despensa:</div>
                                        <div class="badge-list">
                                            <?php foreach($rec['ingredientes_faltando'] as $ing): ?>
                                                <span class="ing-badge missing"><i class="fa-solid fa-plus"></i> <?php echo $ing; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="preparo-text">
                                <strong><i class="fa-solid fa-fire-burner"></i> Como fazer:</strong><br>
                                <?php echo $rec['preparo']; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>

<script>
function adicionarIngrediente(nome) {
    var input = document.getElementById('input-ingredientes');
    var valorAtual = input.value.trim();
    
    if (valorAtual === "") {
        input.value = nome;
    } else {
        var itens = valorAtual.split(',').map(item => item.trim());
        if (!itens.includes(nome)) {
            input.value = valorAtual + ", " + nome;
        }
    }
}

function limparInput() {
    document.getElementById('input-ingredientes').value = "";
}
</script>

</body>
</html><?php
// 1. BANCO DE DADOS DE RECEITAS ANTI-DESPERDÍCIO
$receitas = [
    [
        "nome" => "Omelete Completo de Geladeira",
        "ingredientes" => ["ovo", "tomate", "cebola", "queijo", "presunto", "sal"],
        "preparo" => "Bata os ovos vigorosamente, pique todos os frios e vegetais que tiver, misture tudo e doure na frigideira untada com manteiga ou óleo dos dois lados."
    ],
    [
        "nome" => "Frittata de Legumes Reutilizados",
        "ingredientes" => ["ovo", "batata", "cenoura", "abobrinha", "cebola", "queijo"],
        "preparo" => "Corte os legumes em cubos pequenos e refogue na frigideira. Bata os ovos com sal e jogue por cima dos legumes. Deixe cozinhar em fogo baixo com a tampa fechada até firmar."
    ],
    [
        "nome" => "Ovos no Purgatório (Shakshuka)",
        "ingredientes" => ["ovo", "tomate", "extrato de tomate", "cebola", "alho", "pao"],
        "preparo" => "Faça um molho de tomate bem encorpado e temperado na frigideira. Abra buracos no molho, quebre os ovos diretamente neles, tampe e deixe cozinhar. Coma chuchando o pão."
    ],
    [
        "nome" => "Bruschetta de Pão Amanhecido",
        "ingredientes" => ["pao", "tomate", "alho", "azeite", "queijo"],
        "preparo" => "Corte o pão amanhecido em fatias. Esfregue um dente de alho cru nelas, regue com azeite, cubra com tomates picados temperados e queijo, e leve ao forno até dourar."
    ],
    [
        "nome" => "Rabanada de Forno Fácil",
        "ingredientes" => ["pao", "ovo", "leite", "acucar", "canela"],
        "preparo" => "Passe as fatias de pão no leite doce e depois no ovo batido. Arrume em uma forma untada e asse até dourar. Passe no açúcar com canela."
    ],
    [
        "nome" => "Bolinho de Arroz de Ontem",
        "ingredientes" => ["arroz", "ovo", "farinha de trigo", "queijo", "cebola", "leite"],
        "preparo" => "Misture o arroz de ontem com o ovo, um pouco de leite, queijo ralado e farinha até dar liga de moldar. Faça bolinhas e frite em óleo quente até dourar."
    ],
    [
        "nome" => "Arroz de Forno Cremoso",
        "ingredientes" => ["arroz", "creme de leite", "queijo", "presunto", "tomate", "frango"],
        "preparo" => "Misture o arroz cozido com o creme de leite, o frango desfiado (ou presunto) e os tomates. Cubra com bastante queijo e leve ao forno para gratinar."
    ],
    [
        "nome" => "Carreteiro de Sobras de Churrasco",
        "ingredientes" => ["carne", "calabresa", "arroz", "cebola", "alho", "tomate"],
        "preparo" => "Pique bem as sobras de carne assada ou churrasco. Refogue com cebola, alho, tomate e calabresa. Adicione o arroz, cubra com água fervente e cozinhe até secar."
    ],
    [
        "nome" => "Frango Desfiado Gratinado",
        "ingredientes" => ["frango", "requeijao", "milho", "queijo", "cebola"],
        "preparo" => "Refogue o frango desfiado com cebola e milho. Misture o requeijão, coloque tudo em um refratário, cubra com queijo muçarela e leve ao forno até dourar a superfície."
    ],
    [
        "nome" => "Frittata de Macarrão Amanhecido",
        "ingredientes" => ["macarrao", "ovo", "queijo", "tomate", "cebola"],
        "preparo" => "Bata os ovos temperados com sal. Misture o macarrão que sobrou no prato de ovos. Despeje em uma frigideira com óleo e frite como um omelete grosso dos dois lados."
    ],
    [
        "nome" => "Sopa Quente de Legumes",
        "ingredientes" => ["batata", "cenoura", "abobrinha", "cebola", "alho", "macarrao"],
        "preparo" => "Pique todos os legumes moles ou esquecidos na gaveta. Refogue cebola e alho, jogue os legumes, cubra com água e cozinhe. Adicione um punhado de macarrão quebrado no final."
    ],
    [
        "nome" => "Doce de Banana de Panela",
        "ingredientes" => ["banana", "acucar", "agua", "canela"],
        "preparo" => "Ideal para usar bananas maduras demais. Derreta o açúcar na panela até virar caramelo claro, jogue a água cuidadosamente e depois coloque as bananas fatiadas com canela. Cozinhe até amaciar."
    ]
];

// 2. HIGIENIZAÇÃO DE TEXTO
function limparTexto($texto) {
    $texto = trim(mb_strtolower($texto, 'UTF-8'));
    $mapa = ['á'=>'a','à'=>'a','ã'=>'a','â'=>'a','é'=>'e','ê'=>'e','í'=>'i','ó'=>'o','ô'=>'o','õ'=>'o','ú'=>'u','ç'=>'c'];
    return strtr($texto, $mapa);
}

$receitas_sugeridas = [];
$ingredientes_usuario = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['ingredientes'])) {
    $input_bruto = $_POST['ingredientes'];
    $partes = explode(',', $input_bruto);
    
    foreach ($partes as $parte) {
        $limpo = limparTexto($parte);
        if (!empty($limpo)) {
            $ingredientes_usuario[] = $limpo;
        }
    }

    foreach ($receitas as $receita) {
        $match_count = 0;
        $comuns = [];
        $faltando = [];

        foreach ($receita['ingredientes'] as $ing_receita) {
            $achou = false;
            foreach ($ingredientes_usuario as $ing_user) {
                if (strpos($ing_receita, $ing_user) !== false || strpos($ing_user, $ing_receita) !== false) {
                    $achou = true;
                    break;
                }
            }

            if ($achou) {
                $match_count++;
                $comuns[] = $ing_receita;
            } else {
                $faltando[] = $ing_receita;
            }
        }

        if ($match_count > 0) {
            $receita['match_count'] = $match_count;
            $receita['ingredientes_comuns'] = $comuns;
            $receita['ingredientes_faltando'] = $faltando;
            $receita['aproveitamento'] = round(($match_count / count($receita['ingredientes'])) * 100);
            $receitas_sugeridas[] = $receita;
        }
    }

    usort($receitas_sugeridas, function($a, $b) {
        return $b['aproveitamento'] <=> $a['aproveitamento'];
    });
}

// Coleta ingredientes para a lista clicável
$todos_ingredientes_disponiveis = [];
foreach ($receitas as $r) { $todos_ingredientes_disponiveis = array_merge($todos_ingredientes_disponiveis, $r['ingredientes']); }
$todos_ingredientes_disponiveis = array_unique($todos_ingredientes_disponiveis);
sort($todos_ingredientes_disponiveis);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chefe de Geladeira | Antidesperdício</title>
    <!-- FontAwesome para ícones bonitos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-hover: #059669;
            --background: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-bg: #ffffff;
            --accent-green: #d1fae5;
            --accent-green-text: #065f46;
            --accent-red: #fee2e2;
            --accent-red-text: #991b1b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background-color: var(--background); color: var(--text-main); padding: 40px 20px; line-height: 1.5; }

        .app-container { max-width: 900px; margin: 0 auto; }

        /* Header UI */
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { font-size: 2.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -1px; margin-bottom: 8px; }
        .header h1 span { color: var(--primary); }
        .header p { color: var(--text-muted); font-size: 1.1rem; }

        /* Card Principal de Entrada */
        .search-card { background: var(--card-bg); border-radius: 20px; padding: 35px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; margin-bottom: 40px; }
        .input-wrapper { position: relative; display: flex; align-items: center; margin-bottom: 20px; }
        .input-wrapper i { position: absolute; left: 20px; color: var(--text-muted); font-size: 1.2rem; }
        
        .campo-busca { 
            width: 100%; 
            padding: 18px 20px 18px 55px; 
            font-size: 1rem; 
            border-radius: 14px; 
            border: 2px solid #e2e8f0; 
            background: #f8fafc;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .campo-busca:focus { border-color: var(--primary); background: #fff; outline: none; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }

        /* Tags rápidas */
        .tag-title-area { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .tag-title-area h4 { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
        .btn-clear { background: none; border: none; color: #ef4444; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 5px; transition: opacity 0.2s; }
        .btn-clear:hover { opacity: 0.8; }
        
        .tags-container { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 25px; max-height: 120px; overflow-y: auto; padding-right: 5px; }
        /* Scrollbar customizado para as tags */
        .tags-container::-webkit-scrollbar { width: 4px; }
        .tags-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

        .tag-item { 
            background: #f1f5f9; 
            padding: 8px 14px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            font-weight: 600; 
            color: #475569;
            cursor: pointer; 
            transition:  all 0.2s ease;
            border: 1px solid #e2e8f0;
        }
        .tag-item:hover { background: var(--accent-green); color: var(--accent-green-text); border-color: var(--primary); transform: translateY(-1px); }

        .btn-submit { 
            background: var(--primary); 
            color: white; 
            padding: 16px; 
            border: none; 
            border-radius: 14px; 
            cursor: pointer; 
            width: 100%; 
            font-size: 1.1rem; 
            font-weight: 700; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 10px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }
        .btn-submit:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3); }

        /* Grid de Resultados */
        .results-section h2 { font-size: 1.5rem; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .recipe-grid { display: grid; grid-template-columns: 1fr; gap: 20px; }
        
        @media(min-width: 768px) {
            .recipe-grid { grid-template-columns: 1fr 1fr; }
        }

        /* Card de Receita Estilizado */
        .recipe-card { 
            background: var(--card-bg); 
            border-radius: 18px; 
            padding: 25px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.006);
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .recipe-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05); }

        .recipe-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; gap: 10px; }
        .recipe-title { font-size: 1.25rem; font-weight: 700; color: var(--text-main); line-height: 1.3; }
        
        .match-badge { 
            background: var(--accent-green); 
            color: var(--accent-green-text); 
            padding: 6px 12px; 
            border-radius: 20px; 
            font-size: 0.75rem; 
            font-weight: 700; 
            white-space: nowrap;
        }

        .ingredients-block { margin-bottom: 15px; }
        .block-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 6px; letter-spacing: 0.5px; }
        
        .badge-list { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 12px; }
        .ing-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 600; }
        .ing-badge.have { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
        .ing-badge.missing { background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; }

        .preparo-text { 
            font-size: 0.9rem; 
            color: #475569; 
            background: #f8fafc; 
            padding: 15px; 
            border-radius: 12px; 
            border: 1px solid #f1f5f9;
            margin-top: auto;
        }

        .empty-state { text-align: center; padding: 40px; background: #fff; border-radius: 20px; border: 2px dashed #cbd5e1; color: var(--text-muted); }
        .empty-state i { font-size: 2.5rem; margin-bottom: 15px; color: #94a3b8; }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- Cabeçalho -->
    <header class="header">
        <h1>Chef da <span>Geladeira</span></h1>
        <p>Economize dinheiro e evite o desperdício com o que você já tem em casa.</p>
    </header>

    <!-- Painel de Busca -->
    <section class="search-card">
        <form method="POST" id="form-receitas">
            <div class="input-wrapper">
                <i class="fa-solid :fa-basket-shopping fa-utensils"></i>
                <input type="text" name="ingredientes" id="input-ingredientes" class="campo-busca" 
                       placeholder="Ex: ovo, tomate, pao, arroz..." 
                       value="<?php echo isset($_POST['ingredientes']) ? htmlspecialchars($_POST['ingredientes']) : ''; ?>" required>
            </div>
            
            <div class="tag-title-area">
                <h4><i class="fa-solid fa-hand-pointer"></i> Toque rápido para selecionar:</h4>
                <button type="button" class="btn-clear" onclick="limparInput()"><i class="fa-solid fa-trash-can"></i> Limpar tudo</button>
            </div>

            <div class="tags-container">
                <?php foreach ($todos_ingredientes_disponiveis as $ing_disponivel): ?>
                    <span class="tag-item" onclick="adicionarIngrediente('<?php echo $ing_disponivel; ?>')">+ <?php echo $ing_disponivel; ?></span>
                <?php endforeach; ?>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Combinar Ingredientes
            </button>
        </form>
    </section>

    <!-- Seção de Resultados -->
    <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <section class="results-section">
            <h2><i class="fa-solid fa-kitchen-set"></i> Receitas Sugeridas</h2>
            
            <?php if (empty($receitas_sugeridas)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-triangle-excursion"></i>
                    <p>Nenhuma combinação direta foi encontrada para esses itens.</p>
                    <p style="font-size: 0.85rem; margin-top: 5px;">Dica: Tente palavras básicas como 'ovo', 'pao' ou 'arroz'.</p>
                </div>
            <?php else: ?>
                <div class="recipe-grid">
                    <?php foreach ($receitas_sugeridas as $rec): ?>
                        <div class="recipe-card">
                            <div>
                                <div class="recipe-header">
                                    <h3 class="recipe-title"><?php echo $rec['nome']; ?></h3>
                                    <span class="match-badge"><?php echo $rec['aproveitamento']; ?>% Match</span>
                                </div>
                                
                                <div class="ingredients-block">
                                    <div class="block-title">Você já tem:</div>
                                    <div class="badge-list">
                                        <?php foreach($rec['ingredientes_comuns'] as $ing): ?>
                                            <span class="ing-badge have"><i class="fa-solid fa-check"></i> <?php echo $ing; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <?php if (!empty($rec['ingredientes_faltando'])): ?>
                                        <div class="block-title">Falta na sua despensa:</div>
                                        <div class="badge-list">
                                            <?php foreach($rec['ingredientes_faltando'] as $ing): ?>
                                                <span class="ing-badge missing"><i class="fa-solid fa-plus"></i> <?php echo $ing; ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p class="preparo-text">
                                <strong><i class="fa-solid fa-fire-burner"></i> Como fazer:</strong><br>
                                <?php echo $rec['preparo']; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</div>

<script>
function adicionarIngrediente(nome) {
    var input = document.getElementById('input-ingredientes');
    var valorAtual = input.value.trim();
    
    if (valorAtual === "") {
        input.value = nome;
    } else {
        var itens = valorAtual.split(',').map(item => item.trim());
        if (!itens.includes(nome)) {
            input.value = valorAtual + ", " + nome;
        }
    }
}

function limparInput() {
    document.getElementById('input-ingredientes').value = "";
}
</script>

</body>
</html>