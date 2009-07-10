﻿using System;
using System.Collections.Generic;

using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Business;
using System.Xml.Linq;
using System.Threading;
using System.Text;
using System.IO;
using System.Security.Cryptography;
using System.Collections;
using System.Diagnostics;
using Quantum.Tala.Service;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Framework.Core;
//using CH.Combinations;
 

namespace TestBusiness
{
    public class ABCTest : APIDataEntry
    {
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public TalaUser Player { get; set; }
    }

    class Program
    {       


        static void Main(string[] args)
        {
            TestApplication ta = new TestApplication();
            ta.Start(".");
            ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
            tournamentDTO tour = new tournamentDTO();
            tour.name = "test";
            int nRet = toursvc.CreateTournament(tour);

            ABCTest a = new ABCTest();
            
            a.Player = new TalaUser("danhut", "danhitauthkey");

            Console.WriteLine(a.ToXMLString());


            string sServiceCode = "1001";
            string sAccount = "danhut";
            string sMakerCode = "EAC";

            WSVTCGateTopup.VTCGateTopupSoapClient ws = new TestBusiness.WSVTCGateTopup.VTCGateTopupSoapClient();
            VTCDataSignature.DataSign ds = new VTCDataSignature.DataSign();            
            string orgrinData = sServiceCode + "-" + sAccount + "-" + sMakerCode;
            string Sign = ds.GetSignatureXmlKey(orgrinData, "PrivateKeyTest.xml");
            
            string s = ws.CheckAccountEXISTS(sServiceCode, sAccount, sMakerCode,  Sign);

            Console.WriteLine(Sign); 
            Console.WriteLine(s);
            Console.ReadLine();


            #region Test performance with XElement and StringBuilder
            
            // StringBuilder faster 2x with Xelement, but not signical
            
            //XElement superX = new XElement("root");
            //StringBuilder superString = new StringBuilder();

            //DateTime start;
            //DateTime end;
            //TimeSpan elap;
            
            //start = DateTime.Now;
            //for (int i = 0; i < 500000; i++)
            //{
            //    XElement x = new XElement("q",
            //        new XAttribute("a1", "abc"),
            //        new XAttribute("a2", "abc"),
            //        new XAttribute("a3", "abc"),
            //        new XAttribute("a4", "abc"),

            //        new XElement("e1", "abc"),
            //        new XElement("e2", "abc"),
            //        new XElement("e3", "abc"),
            //        new XElement("e4", "abc"),
            //        new XElement("e5", "abc"),
            //        new XElement("e",
            //            new XElement("ec1", "abc"),
            //            new XElement("ec2", "abc"),
            //            new XElement("ec3", "abc"),
            //            new XElement("ec4", "abc")
            //            )
            //        );
            //    superX.Add(x);//x.ToString();
            //}
            
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);

            //start = DateTime.Now;
            ////superX.ToString();
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);


            //Thread.Sleep(1000);



            //start = DateTime.Now;
            //for (int i = 0; i < 500000; i++)
            //{
            //    string s1 = "<e1>abc</e1><e1>abc</e1><e1>abc</e1><e1>abc</e1><e1>abc</e1>";
            //    string s2 = "<e><ec1>abc</ec1><ec1>abc</ec1><ec1>abc</ec1><ec1>abc</ec1><e>";
            //    string s = string.Format(
            //        "<q a1='{2}' a2='{3}' a3='{4}' a4='{5}' >{0}{1}</q>",
            //        s1, s2, "abc", "abc", "abc", "abc"
            //        );
            //    superString.Append(s);
            //}
            //superString.ToString();
            //end = DateTime.Now;
            //elap = end.Subtract(start);
            //Console.WriteLine(elap.TotalMilliseconds);

            #endregion


            //IUser u = AuthenticationProvider.Authenticate("*","vu1","quantum");            
            //Console.WriteLine(u.Username);
            //IUser u2 = AuthenticationProvider.Authenticate("*", "v2", "quantum");
            
        }


        public static string EncryptString(string inputString, int dwKeySize,
                             string xmlString)
        {
            // TODO: Add Proper Exception Handlers
            RSACryptoServiceProvider rsaCryptoServiceProvider = new RSACryptoServiceProvider(dwKeySize);
            rsaCryptoServiceProvider.FromXmlString(xmlString);
            int keySize = dwKeySize / 8;
            byte[] bytes = Encoding.UTF32.GetBytes(inputString);
            // The hash function in use by the .NET RSACryptoServiceProvider here 
            // is SHA1
            // int maxLength = ( keySize ) - 2 - 
            //              ( 2 * SHA1.Create().ComputeHash( rawBytes ).Length );
            int maxLength = keySize - 42;
            int dataLength = bytes.Length;
            int iterations = dataLength / maxLength;
            StringBuilder stringBuilder = new StringBuilder();
            for (int i = 0; i <= iterations; i++)
            {
                byte[] tempBytes = new byte[
                        (dataLength - maxLength * i > maxLength) ? maxLength :
                                                      dataLength - maxLength * i];
                Buffer.BlockCopy(bytes, maxLength * i, tempBytes, 0,
                                  tempBytes.Length);
                byte[] encryptedBytes = rsaCryptoServiceProvider.Encrypt(tempBytes,
                                                                          false);
                // Be aware the RSACryptoServiceProvider reverses the order of 
                // encrypted bytes. It does this after encryption and before 
                // decryption. If you do not require compatibility with Microsoft 
                // Cryptographic API (CAPI) and/or other vendors. Comment out the 
                // next line and the corresponding one in the DecryptString function.
                Array.Reverse(encryptedBytes);
                // Why convert to base 64?
                // Because it is the largest power-of-two base printable using only 
                // ASCII characters
                stringBuilder.Append(Convert.ToBase64String(encryptedBytes));
            }
            return stringBuilder.ToString();
        }

        public static string DecryptString(string inputString, int dwKeySize,
                                     string xmlString)
        {
            // TODO: Add Proper Exception Handlers
            RSACryptoServiceProvider rsaCryptoServiceProvider
                                     = new RSACryptoServiceProvider(dwKeySize);
            rsaCryptoServiceProvider.FromXmlString(xmlString);
            int base64BlockSize = ((dwKeySize / 8) % 3 != 0) ?
              (((dwKeySize / 8) / 3) * 4) + 4 : ((dwKeySize / 8) / 3) * 4;
            int iterations = inputString.Length / base64BlockSize;
            ArrayList arrayList = new ArrayList();
            for (int i = 0; i < iterations; i++)
            {
                byte[] encryptedBytes = Convert.FromBase64String(
                     inputString.Substring(base64BlockSize * i, base64BlockSize));
                // Be aware the RSACryptoServiceProvider reverses the order of 
                // encrypted bytes after encryption and before decryption.
                // If you do not require compatibility with Microsoft Cryptographic 
                // API (CAPI) and/or other vendors.
                // Comment out the next line and the corresponding one in the 
                // EncryptString function.
                Array.Reverse(encryptedBytes);
                arrayList.AddRange(rsaCryptoServiceProvider.Decrypt(
                                    encryptedBytes, false));
            }
            return Encoding.UTF32.GetString(arrayList.ToArray(
                                      Type.GetType("System.Byte")) as byte[]);
        }


        static void TestU()
        {
            List<Card> cardList = new List<Card>();

            //Card card = new Card("02", "c");
            //cardList.Add(card);
            //card = new Card("02", "d");
            //cardList.Add(card);
            //card = new Card("02", "p");
            //cardList.Add(card);
            //card = new Card("02", "t");
            //cardList.Add(card);
            //card = new Card("03", "d");
            //cardList.Add(card);
            //card = new Card("04", "d");
            //cardList.Add(card);
            //card = new Card("05", "d");
            //cardList.Add(card);
            //card = new Card("06", "d");
            //cardList.Add(card);
            //card = new Card("05", "c");
            //cardList.Add(card);
            //card = new Card("05", "p");
            //cardList.Add(card);

            DateTime start;
            DateTime end;
            TimeSpan elap;

            start = DateTime.Now;
            Card card = new Card("01", "t");
            cardList.Add(card);
            card = new Card("02", "c");
            cardList.Add(card);
            //card = new Card("02", "d");
            //cardList.Add(card);
            //card = new Card("02", "p");
            //cardList.Add(card);
            //card = new Card("02", "t");
            //cardList.Add(card);
            card = new Card("03", "t");
            cardList.Add(card);
            card = new Card("04", "t");
            cardList.Add(card);
            card = new Card("05", "c");
            cardList.Add(card);
            card = new Card("05", "d");
            cardList.Add(card);
            card = new Card("05", "t");
            cardList.Add(card);

            List<Card> baiDaAn = new List<Card>();
            baiDaAn.Add(new Card("02", "t"));
            baiDaAn.Add(new Card("02", "p"));
            baiDaAn.Add(new Card("02", "d"));

            int count = UUtil.CheckU(cardList, baiDaAn);
            end = DateTime.Now;
            elap = end.Subtract(start);
            System.Console.Out.WriteLine("========== U result: " + count + "====== elapse: " + elap.TotalMilliseconds);

            Console.ReadLine();
        }

        static int uCount = 0;

        static void TestMain(string[] args)
        {
            //Card[] input = Card.CARD_SET;
            //Combinations<Card> combinations = new Combinations<Card>(input, 10);
            //int i = 0;
            //FileStream fs = new FileStream("C:/u.txt", FileMode.Create, FileAccess.Write);
            //StreamWriter sw = new StreamWriter(fs);
            //sw.WriteLine("Combination lists: ");
            //foreach (Card[] combination in combinations)
            //{
            //    MakeCardListAndCheckU(combination, sw);
            //    i++;
            //}
            //sw.WriteLine("=====================");
            //sw.WriteLine("Total Combination count: " + i);
            //sw.WriteLine("Total U Combination count: " + uCount);

            //sw.Close();
            //fs.Close();
            //Console.WriteLine("DONE!!!");
            //Console.ReadLine();

        }

        //static void MakeCardListAndCheckU(Card[] cardArr, StreamWriter sw)
        //{
        //    List<Card> cardList = new List<Card>(10);
        //    for (int i = 0; i < cardArr.Length; i++)
        //    {
        //        cardList.Add(cardArr[i]);
        //    }
        //    int count = UUtil.CheckU(cardList);
        //    if (count >= 9)
        //    {
        //        sw.WriteLine("Combination " + uCount + ": Count = " + count);
        //        WriteCombination(cardArr, sw);
        //        uCount++;
        //    }
        //}

        //static void WriteCombination(Card[] combination, StreamWriter sw)
        //{
        //    for (int i = 0; i < combination.Length; i++)
        //    {
        //        Card card = combination[i];
        //        sw.Write(card.ToString() + " ");
        //    }
        //    sw.WriteLine("");
        //    sw.WriteLine("=======================");
        //}

    }
}
