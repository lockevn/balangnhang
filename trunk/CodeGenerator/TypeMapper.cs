using System;
using System.Collections;
using System.Xml;

namespace GURUCORE.GForm.CodeGenerator
{
	/// <summary>
	/// Summary description for TypeMapper.
	/// </summary>
	public class TypeMapper
	{
		protected Hashtable m_htCSharpType;
		protected Hashtable m_htCSharpTypePrefix;

		public TypeMapper()
		{
		}

		public TypeMapper(string p_sFile)
		{
			m_htCSharpType = new Hashtable();
			m_htCSharpTypePrefix = new Hashtable();

			XmlTextReader oReader = new XmlTextReader(p_sFile);
			XmlDocument oXmlDoc = new XmlDocument();
			oXmlDoc.Load(oReader);
			XmlElement oRootElm = oXmlDoc.DocumentElement;
			foreach (XmlNode oTypeNode in oRootElm.ChildNodes)
			{
				if (oTypeNode.NodeType == XmlNodeType.Element)
				{
					string sName = oTypeNode.Attributes["name"].Value;
					string sPrefix = oTypeNode.Attributes["prefix"].Value;
					string sType = oTypeNode.InnerText;

					m_htCSharpType.Add(sName,sType);
					m_htCSharpTypePrefix.Add(sName,sPrefix);
				}
			}
			oReader.Close();
		}

		public string GetCSharpType(string p_sDBType)
		{
			if (m_htCSharpType[p_sDBType] != null)
			{
				return m_htCSharpType[p_sDBType].ToString();
			}
			else
			{
				return "object";
			}
		}

		public string GetCSharpTypePrefix(string p_sDBType)
		{
			if (m_htCSharpTypePrefix[p_sDBType] != null)
			{
				return m_htCSharpTypePrefix[p_sDBType].ToString();
			}
			else
			{
				return "";
			}
		}
	}
}
