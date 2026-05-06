<?php

require_once __DIR__ . '/vendor/autoload.php';

$kernel = new \App\Kernel('dev', true);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine')->getManager();
$user = $em->getRepository(\App\Entity\User::class)->findOneBy(['email' => 'test@alecone.fr']);

if ($user) {
    $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
    $em->flush();
    echo "Role mis a jour avec succes\n";
} else {
    echo "Utilisateur non trouve\n";
}
