<?php
try {
    $pdo = new PDO('pgsql:host=aws-1-us-west-2.pooler.supabase.com;port=5432;dbname=postgres;sslmode=require', 'postgres.gkrjyxlllgvhghejmsoj', 'AmericanNicaraguanSchool.');
    echo "CONECTADO\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
