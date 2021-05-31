<?php


namespace App\Controller;

use App\Entity\Model;
use App\Entity\Offer;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Sell;
use App\Entity\User;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\SellRepository;
use App\Repository\UserVerificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{

    /**
     * @Route("/", name="product.index")
     * @param SellRepository $sellRepository
     * @param UserVerificationRepository $verificationRepository
     * @return Response
     */
    public function index(SellRepository $sellRepository, UserVerificationRepository $verificationRepository): Response
    {
        $is_merchant = $verificationRepository->findOneBy(['requestingUser' => $this->getUser()]);
        $merchant = $is_merchant ? $is_merchant->getStatus() === 1 ? $is_merchant : false : false;

        $products = [];
        $sells = $sellRepository->findBy(['soldBy' => $this->getUser()]);
        foreach ($sells as $sell) {
            $products[] = $sell->getProduct();
        }

        return $this->render('/product/index.html.twig', [
            'products' => $products,
            'current_page' => 'product',
            'merchant' => $merchant
        ]);

    }

    /**
     * @Route("/new", name="product.new")
     * @param Request $request
     * @param SluggerInterface $slugger
     * @return Response
     */
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $product->setDepositDate(new \DateTime());
            $product->setPrice($product->getPrice());
            $product->setModel($product->getModel());
            $entityManager->persist($product);

            // images
            $pictures = $form->get('pictures')->getData();
            // this condition is needed because the 'pictures' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($pictures) {
                $originalFilename = pathinfo($pictures->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictures->guessExtension();

                // Move the file to the directory where brochures are stored
                try {

                    $picture = new Picture();
                    $picture->setName($newFilename);
                    $picture->setExtension(
                        Picture::EXTENTIONS[$pictures->guessExtension()]
                    );
                    $picture->setSize($pictures->getSize());
                    $picture->setProduct($product);

                    $pictures->move(
                        $this->getParameter('pictures_directory'),
                        $newFilename
                    );

                    $entityManager->persist($picture);

                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

            }

            $sell = new Sell();
            $sell->setProduct($product);
            $sell->setStatus(0);
            $sell->setDepositDate($product->getDepositDate());
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $this->getUser()->getUsername()]);
            $sell->setSoldBy($user);
            $entityManager->persist($sell);

            $entityManager->flush();

            return $this->redirectToRoute('product.index');
        }

        return $this->render('/product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
            'current_page' => 'product'
        ]);
    }

    /**
     * @Route("/{id}", name="product.show")
     * @param Product $product
     * @return Response
     */
    public function show(Product $product): Response
    {
        $pictures = $this->getDoctrine()->getRepository(Picture::class)->findBy(['product' => $product]);

        return $this->render('/product/show.html.twig', [
            'product' => $product,
            'pictures' => $pictures,
            'current_page' => 'product'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product.edit")
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('product.index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="product.delete", methods={"POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product.index');
    }

    /**
     * @Route("/simulate/v1", name="product.simulate", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function simulate(Request $request): Response
    {
        $condition = $request->query->get('condition');
        $modelId = $request->query->get('model');

        $model = $this->getDoctrine()->getRepository(Model::class)->find($modelId);

        $offer = $this->getDoctrine()->getRepository(Offer::class)->findOneBy([
            'productCondition' => $condition,
            'model' => $model
        ]);

        if ($offer) {
            return $this->json([
                'price' => $offer->getAmount()
            ]);
        }

        return $this->json([
            'error' => 'Nous n\'avons aucun prix correspondant à votre simulation.'
        ]);
    }

}