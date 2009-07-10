using System;

namespace GURUCORE.GForm.CodeGenerator
{
	/// <summary>
	/// Summary description for TemplateTool.
	/// </summary>
	public class TemplateTool
	{
		private string[] m_arrTablePrefixes;

		public TemplateTool(string p_sTablePrefixes)
		{
			if (p_sTablePrefixes.EndsWith(","))
			{
				p_sTablePrefixes = p_sTablePrefixes.Substring(0,p_sTablePrefixes.Length - 1);
			}
			m_arrTablePrefixes = p_sTablePrefixes.Split(',');
		}

		public string TruncatePrefix(string p_sTableName)
		{
			foreach (string sPrefix in m_arrTablePrefixes)
			{
				if (p_sTableName.StartsWith(sPrefix))
				{
					string sResult = p_sTableName.Substring(sPrefix.Length,p_sTableName.Length - sPrefix.Length);
					return sResult;
				}
			}
			return p_sTableName;
		}

		public string GetPrefix(string p_sTableName)
		{
			foreach (string sPrefix in m_arrTablePrefixes)
			{
				if (p_sTableName.StartsWith(sPrefix))
				{
					return sPrefix;
				}
			}
			return string.Empty;
		}

		public string Append(string p_sFirst, string p_sSecond)
		{
			return p_sFirst + p_sSecond;
		}

		public string Iff(string p_sFirst, string p_sSecond)
		{
			return (p_sFirst == p_sSecond) ? "true" : "false";
		}

		public string EvaluateNull(string p_sInput)
		{
			if (p_sInput != string.Empty)
			{
				return "\"" + p_sInput + "\"";
			}
			else
			{
				return "null";
			}
		}
	}
}
